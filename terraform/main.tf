
terraform {
  required_version = "~> 1.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "6.18.0"
    }
  }

  # Comentamos temporalmente el backend S3
  # backend "s3" {
  #   bucket         = "repository-terraform-states-prod"
  #   key            = "repository.tfstate"
  #   region         = "us-east-1"
  #   encrypt        = true
  #   use_lockfile   = true
  # }
}

provider "aws" {
  region = "us-east-1"
}

# Bucket S3 para hosting estático del frontend Angular
resource "aws_s3_bucket" "frontend_static" {
  bucket = "repository-terraform-states-prod"

  # Permite eliminar el bucket aunque tenga contenido
  force_destroy = true

  tags = {
    Name        = "Frontend Static Hosting"
    Environment = "prod"
    Purpose     = "static-website"
  }
}

# Configuración para hosting de sitio web estático
resource "aws_s3_bucket_website_configuration" "frontend_static_website" {
  bucket = aws_s3_bucket.frontend_static.id

  index_document {
    suffix = "index.html"
  }

  error_document {
    key = "index.html" # Para SPAs como Angular, redirigir errores a index.html
  }

  routing_rule {
    condition {
      http_error_code_returned_equals = "404"
    }
    redirect {
      replace_key_with = "index.html"
    }
  }
}

# Configurar acceso público para el bucket
resource "aws_s3_bucket_public_access_block" "frontend_static_pab" {
  bucket = aws_s3_bucket.frontend_static.id

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false
}

# Política para permitir acceso público de lectura
resource "aws_s3_bucket_policy" "frontend_static_policy" {
  bucket = aws_s3_bucket.frontend_static.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "PublicReadGetObject"
        Effect    = "Allow"
        Principal = "*"
        Action    = "s3:GetObject"
        Resource  = "${aws_s3_bucket.frontend_static.arn}/*"
      }
    ]
  })

  depends_on = [aws_s3_bucket_public_access_block.frontend_static_pab]
}

# Outputs
output "website_url" {
  description = "URL del sitio web estático"
  value       = "http://${aws_s3_bucket_website_configuration.frontend_static_website.website_endpoint}"
}

output "bucket_name" {
  description = "Nombre del bucket S3"
  value       = aws_s3_bucket.frontend_static.id
}