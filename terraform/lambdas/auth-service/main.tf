data "archive_file" "auth_service" {
  type        = "zip"
  source_dir  = "${path.module}/../../../backend/auth-service"
  output_path = "${path.module}/auth-service.zip"
}

resource "aws_lambda_function" "auth_service" {
  filename         = data.archive_file.auth_service.output_path
  function_name    = "${var.project_name}-auth-service"
  role            = aws_iam_role.lambda_exec.arn
  handler         = "handler.php"
  runtime         = "provided.al2023"

  environment {
    variables = {
      APP_ENV              = "production"
      DYNAMODB_TABLE_ADMINS = var.dynamodb_admins_table
      JWT_SECRET           = var.jwt_secret
    }
  }

  layers = [var.bref_php_layer]

  depends_on = [data.archive_file.auth_service]
}

resource "aws_iam_role" "lambda_exec" {
  name = "${var.project_name}-lambda-auth"

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
  name = "${var.project_name}-dynamodb-auth"
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
          var.dynamodb_admins_table_arn,
          "arn:aws:dynamodb:${var.region}:${var.account_id}:table/${var.dynamodb_admins_table}/index/*"
        ]
      }
    ]
  })
}
