# Prepare Lambda package with shared dependencies
resource "null_resource" "prepare_advise_package" {
  triggers = {
    # Trigger rebuild when any PHP file changes
    src_hash = sha256(join("", [
      for f in fileset("${path.module}/../../../backend/advise-service", "**/*.php") :
      filesha256("${path.module}/../../../backend/advise-service/${f}")
    ]))
  }

  provisioner "local-exec" {
    command = "cd ${path.module}/../../../backend/advise-service && bash prepare-lambda.sh"
  }
}

data "archive_file" "advise_service" {
  type        = "zip"
  source_dir  = "${path.module}/../../../backend/advise-service/lambda-build"
  output_path = "${path.module}/advise-service.zip"

  depends_on = [null_resource.prepare_advise_package]
}

resource "aws_lambda_function" "advise_service" {
  filename         = data.archive_file.advise_service.output_path
  function_name    = "${var.project_name}-advise-service"
  role             = aws_iam_role.lambda_exec.arn
  handler          = "api/index.php"
  runtime          = "provided.al2023"
  timeout          = 30
  memory_size      = 512
  source_code_hash = data.archive_file.advise_service.output_base64sha256

  environment {
    variables = {
      APP_ENV                 = "production"
      DYNAMODB_TABLE_REQUESTS = var.dynamodb_requests_table
      JWT_SECRET              = var.jwt_secret
      SNS_TOPIC_ARN           = var.sns_topic_arn
    }
  }

  # Using Bref PHP 8.1 FPM layer for API Gateway requests
  layers = ["arn:aws:lambda:${var.region}:534081306603:layer:php-81-fpm:59"]

  depends_on = [data.archive_file.advise_service]
}

resource "aws_iam_role" "lambda_exec" {
  name = "${var.project_name}-lambda-advise"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "lambda.amazonaws.com"
        }
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "lambda_basic_execution" {
  role       = aws_iam_role.lambda_exec.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AWSLambdaBasicExecutionRole"
}

resource "aws_iam_role_policy" "dynamodb" {
  name = "${var.project_name}-dynamodb-advise"
  role = aws_iam_role.lambda_exec.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "dynamodb:GetItem",
          "dynamodb:PutItem",
          "dynamodb:UpdateItem",
          "dynamodb:Query",
          "dynamodb:Scan"
        ]
        Resource = [
          var.dynamodb_requests_table_arn,
        ]
      }
      ,
      {
        Effect = "Allow"
        Action = [
          "sns:Publish"
        ]
        Resource = [
          var.sns_topic_arn
        ]
      }
    ]
  })
}
