import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RequestService } from '../../../core/services/request.service';

@Component({
  selector: 'app-register-page',
  templateUrl: './register-page.component.html',
  styleUrls: ['./register-page.component.scss']
})
export class RegisterPageComponent implements OnInit {
  registerForm: FormGroup = new FormGroup({});
  isSubmitting = false;
  submissionError: string | null = null;
  submissionSuccess = false;

  constructor(
    private fb: FormBuilder,
    private requestService: RequestService // Inject the service
  ) { }

  ngOnInit(): void {
    this.registerForm = this.fb.group({
      nombre: ['', [Validators.required]],
      correo: ['', [Validators.required, Validators.email]],
      telefono: [''] // Optional field
    });
  }

  onSubmit(): void {
    if (this.registerForm.invalid || this.isSubmitting) {
      return;
    }

    this.isSubmitting = true;
    this.submissionError = null;
    this.submissionSuccess = false;

    this.requestService.createRequest(this.registerForm.value).subscribe({
      next: (response) => {
        console.log('Submission successful!', response);
        this.submissionSuccess = true;
        this.registerForm.reset();
        this.isSubmitting = false;
      },
      error: (error) => {
        console.error('Submission failed:', error);
        this.submissionError = 'Ocurrió un error al enviar la solicitud. Por favor, inténtalo de nuevo.';
        this.isSubmitting = false;
      }
    });
  }
}