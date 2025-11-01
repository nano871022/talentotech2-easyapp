
terraform {
  required_version = "~> 1.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "6.18.0"
    }
  }

  backend "s3" {
    bucket         = "repository-terraform-states-prod"
    key            = "repository.tfstate"
    region         = "us-east-1"
    encrypt        = true
    use_lockfile   = true
  }
}

provider "aws" {
  region = "us-east-1"
}