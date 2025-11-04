data "archive_file" "advise_service" {
  type        = "zip"
  source_dir  = "${path.module}/../../../backend/advise-service"
  output_path = "${path.module}/advise-service.zip"
}

resource "aws_lambda_function" "advise_service" {
  filename         = data.archive_file.advise_service.output_path
  function_name    = "${var.project_name}-advise-service"
  role            = aws_iam_role.lambda_exec.arn
  handler         = "handler.php"
  runtime         = "provided.al2023"

  environment {
    variables = {
      APP_ENV                = "production"
      DYNAMODB_TABLE_REQUESTS = var.dynamodb_requests_table
      JWT_SECRET             = var.jwt_secret
      AWS_REGION             = var.region
      SNS_TOPIC_ARN         = var.sns_topic_arn
    }
  }

  layers = [var.bref_php_layer]

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
