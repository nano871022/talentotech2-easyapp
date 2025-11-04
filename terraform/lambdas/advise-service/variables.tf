variable "project_name" {
  description = "The name of the project, used to prefix resources"
  type        = string
}

variable "dynamodb_requests_table" {
  description = "The name of the DynamoDB table for requests"
  type        = string
}

variable "dynamodb_requests_table_arn" {
  description = "The ARN of the DynamoDB table for requests"
  type        = string
}

variable "jwt_secret" {
  description = "The JWT secret for authentication"
  type        = string
  sensitive   = true
}

variable "region" {
  description = "The AWS region"
  type        = string
}

variable "account_id" {
  description = "The AWS account ID"
  type        = string
}

variable "bref_php_layer" {
  description = "The ARN of the Bref PHP layer"
  type        = string
}

variable "sns_topic_arn" {
  description = "The ARN of the SNS topic to publish notifications"
  type        = string
  default     = ""
}