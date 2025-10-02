import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-register-page',
  templateUrl: './register-page.component.html',
  styleUrls: ['./register-page.component.scss'],
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, HttpClientModule]
})
export class RegisterPageComponent implements OnInit {
  form!: FormGroup;
  cursos: string[] = ['Ingles', 'Franses', 'Chino', 'Portugues', 'Aleman'];
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      name: ['', [Validators.required]],
      phone: ['', [Validators.required]],
      cursos: this.fb.array([], [Validators.required])
    });
  }

  toggleCurso(curso: string): void {
    const cursosArray = this.form.get('cursos') as FormArray;
    if (cursosArray.value.includes(curso)) {
      cursosArray.removeAt(cursosArray.value.findIndex((c: string) => c === curso));
    } else {
      cursosArray.push(this.fb.control(curso));
    }
  }

  onSubmit(): void {
    if (this.form.invalid || this.isSubmitting) {
      return;
    }
    this.isSubmitting = true;

    this.http.post('/v1/requests', this.form.value).subscribe({
      next: (response) => {
        console.log('Request successful!', response);
        this.isSubmitting = false;
        // Optionally, navigate to a success page or show a success message
        this.router.navigate(['/']);
      },
      error: (error) => {
        console.error('Request failed:', error);
        this.isSubmitting = false;
        // Optionally, show an error message to the user
      }
    });
  }

  close(): void {
    this.router.navigate(['/']); // Navigate to home or any other route
  }
}