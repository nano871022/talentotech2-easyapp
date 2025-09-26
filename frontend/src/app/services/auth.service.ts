import { Injectable, inject, signal } from '@angular/core';
import { ApiService } from './api.service';
import { Observable, of } from 'rxjs';
import { tap, map, catchError } from 'rxjs/operators';

export interface AuthResponse {
  success: boolean;
  message?: string;
  error?: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiService = inject(ApiService);
  private _isAuthenticated = signal<boolean>(false);

  // Expone el estado de autenticación como una señal de solo lectura.
  public isAuthenticated = this._isAuthenticated.asReadonly();

  constructor() {
    // Al iniciar el servicio, se podría verificar la sesión existente.
    this.checkSession().subscribe();
  }

  /**
   * Intenta iniciar sesión en el servidor.
   * @param credentials Objeto con `usuario` y `password`.
   * @returns Un observable que emite `true` en caso de éxito, `false` en caso contrario.
   */
  login(credentials: { usuario: string, password: string }): Observable<boolean> {
    return this.apiService.post<AuthResponse>('/login.php', credentials).pipe(
      tap(response => {
        if (response.success) {
          this._isAuthenticated.set(true);
        }
      }),
      map(response => response.success),
      catchError(() => {
        this._isAuthenticated.set(false);
        return of(false);
      })
    );
  }

  /**
   * Cierra la sesión en el servidor.
   * @returns Un observable que emite `true` si el logout fue exitoso.
   */
  logout(): Observable<boolean> {
    return this.apiService.post<AuthResponse>('/logout.php', {}).pipe(
      tap(() => {
        this._isAuthenticated.set(false); // Actualiza el estado localmente sin esperar respuesta.
      }),
      map(response => response.success),
      catchError(() => {
        this._isAuthenticated.set(false);
        return of(false);
      })
    );
  }

  /**
   * Verifica si la sesión del usuario sigue activa en el backend.
   * Llama a un endpoint protegido para validar la sesión.
   */
  checkSession(): Observable<boolean> {
    return this.apiService.get<any>('/solicitudes.php').pipe(
      map(response => {
        if (response.success) {
          this._isAuthenticated.set(true);
          return true;
        }
        this._isAuthenticated.set(false);
        return false;
      }),
      catchError(() => {
        this._isAuthenticated.set(false);
        return of(false);
      })
    );
  }
}