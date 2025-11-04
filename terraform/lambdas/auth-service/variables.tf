variable "project_name" {
  description = "The name of the project, used to prefix resources"
  type        = string
}

variable "dynamodb_admins_table" {
  description = "The name of the DynamoDB table for admins"
  type        = string
}

variable "dynamodb_admins_table_arn" {
  description = "The ARN of the DynamoDB table for admins"
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
  default     = "arn:aws:lambda:us-east-1:534081306603:layer:php-81-fpm:59"
}

variable "db_driver" {
  description = "The database driver to be used by the advise service"
  type        = string
  default = "dynamodb"
}

variable "region" {
  description = "The AWS region"
  type        = string
  default     = "us-east-1" 
}