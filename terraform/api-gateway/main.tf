resource "aws_api_gateway_rest_api" "main" {
  body = jsonencode({
    openapi = "3.0.1"
    info = {
      title   = "${var.project_name}-api"
      version = "1.0"
    }
    paths = {
      "/auth" = {
        x-amazon-apigateway-any-method = {
          x-amazon-apigateway-integration = {
            httpMethod           = "POST"
            type                 = "AWS_PROXY"
            uri                  = module.lambda_auth.invoke_arn
          }
        }
      }
      "/api" = {
        x-amazon-apigateway-any-method = {
          x-amazon-apigateway-integration = {
            httpMethod           = "POST"
            type                 = "AWS_PROXY"
            uri                  = module.lambda_advise.invoke_arn
          }
        }
      }
    }
  })

  name = "${var.project_name}-api"
}

resource "aws_api_gateway_deployment" "main" {
  rest_api_id = aws_api_gateway_rest_api.main.id

  triggers = {
    redeployment = sha1(jsonencode(aws_api_gateway_rest_api.main.body))
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_api_gateway_stage" "main" {
  deployment_id = aws_api_gateway_deployment.main.id
  rest_api_id   = aws_api_gateway_rest_api.main.id
  stage_name    = var.environment
}

# Permisos para que API Gateway invoque las Lambdas
resource "aws_lambda_permission" "auth" {
  statement_id  = "AllowExecutionFromAPIGateway"
  action        = "lambda:InvokeFunction"
  function_name = var.lambda_auth_function_name
  principal     = "apigateway.amazonaws.com"
  source_arn    = "${aws_api_gateway_rest_api.main.execution_arn}/*/*"
}

resource "aws_lambda_permission" "advise" {
  statement_id  = "AllowExecutionFromAPIGateway"
  action        = "lambda:InvokeFunction"
  function_name = var.lambda_advise_function_name
  principal     = "apigateway.amazonaws.com"
  source_arn    = "${aws_api_gateway_rest_api.main.execution_arn}/*/*"
}
