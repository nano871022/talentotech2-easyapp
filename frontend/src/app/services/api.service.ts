import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private http = inject(HttpClient);

  private getHeaders(): HttpHeaders {
    return new HttpHeaders({
      'Content-Type': 'application/json'
    });
  }

  get<T>(url: string): Observable<T> {
    // `withCredentials` es clave para que el navegador envíe las cookies de sesión.
    return this.http.get<T>(`/api${url}`, { withCredentials: true });
  }

  post<T>(url: string, body: any): Observable<T> {
    return this.http.post<T>(`/api${url}`, JSON.stringify(body), {
      headers: this.getHeaders(),
      withCredentials: true
    });
  }
}