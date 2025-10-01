import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root' // Provided in the root injector, available application-wide
})
export class AuthService {
  private apiUrl = `${environment.API_BASE_URL}/auth`;

  constructor(private http: HttpClient) { }

  /**
   * Sends login credentials to the backend API.
   * @param credentials An object containing the user's 'usuario' and 'password'.
   * @returns An Observable with the response from the API.
   */
  login(credentials: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials);
  }

  // In a real application, we would add methods for logout, token management, etc.
  // logout(): void { ... }
  // getToken(): string | null { ... }
  // isAuthenticated(): boolean { ... }
}