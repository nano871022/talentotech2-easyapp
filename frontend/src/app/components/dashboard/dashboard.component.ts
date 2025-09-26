import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';

export interface Solicitud {
  id: number;
  nombre: string;
  correo: string;
  telefono: string | null;
  estado: string;
  created_at: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  private apiService = inject(ApiService);
  private authService = inject(AuthService);
  private router = inject(Router);

  solicitudes: Solicitud[] = [];
  isLoading = true;
  errorMessage: string | null = null;

  ngOnInit(): void {
    this.loadSolicitudes();
  }

  loadSolicitudes(): void {
    this.isLoading = true;
    this.errorMessage = null;

    this.apiService.get<{ success: boolean; data: Solicitud[] }>('/solicitudes.php').subscribe({
      next: (response) => {
        if (response.success) {
          this.solicitudes = response.data;
        } else {
          this.errorMessage = 'No se pudieron cargar las solicitudes.';
        }
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Error de conexión. Verifique su sesión e inténtelo de nuevo.';
        this.isLoading = false;
      }
    });
  }

  logout(): void {
    this.authService.logout().subscribe(() => {
      this.router.navigate(['/login']);
    });
  }
}