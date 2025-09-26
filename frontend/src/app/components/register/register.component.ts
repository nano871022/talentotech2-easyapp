import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  private fb = inject(FormBuilder);
  private apiService = inject(ApiService);
  private router = inject(Router);

  registerForm = this.fb.group({
    nombre: ['', [Validators.required]],
    correo: ['', [Validators.required, Validators.email]],
    telefono: ['']
  });

  successMessage: string | null = null;
  errorMessage: string | null = null;

  onSubmit(): void {
    if (this.registerForm.invalid) {
      this.errorMessage = 'Por favor, corrija los errores en el formulario.';
      return;
    }

    this.errorMessage = null;
    this.successMessage = null;
    const formData = this.registerForm.getRawValue();

    this.apiService.post<any>('/register.php', formData).subscribe({
      next: (response) => {
        if (response.success) {
          this.successMessage = '¡Solicitud enviada con éxito! Gracias por contactarnos.';
          this.registerForm.reset();
        } else {
          this.errorMessage = response.error || 'Ocurrió un error al enviar su solicitud.';
        }
      },
      error: () => {
        this.errorMessage = 'Ocurrió un error en el servidor. Inténtelo de nuevo más tarde.';
      }
    });
  }
}