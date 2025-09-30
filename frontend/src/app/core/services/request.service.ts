import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

// Define an interface for the request data for type safety
export interface AdvisoryRequest {
  id?: number;
  nombre: string;
  correo: string;
  telefono?: string;
  estado?: string;
  fecha_solicitud?: string;
}

@Injectable({
  providedIn: 'root'
})
export class RequestService {
  private apiUrl = `${environment.API_BASE_URL}/requests`;

  constructor(private http: HttpClient) { }

  /**
   * Submits a new advisory request to the backend.
   * @param requestData The data from the registration form.
   * @returns An Observable with the API response.
   */
  createRequest(requestData: AdvisoryRequest): Observable<any> {
    return this.http.post(this.apiUrl, requestData);
  }

  /**
   * Fetches all advisory requests from the backend.
   * @returns An Observable containing an array of AdvisoryRequest objects.
   */
  getRequests(): Observable<AdvisoryRequest[]> {
    return this.http.get<AdvisoryRequest[]>(this.apiUrl);
  }
}