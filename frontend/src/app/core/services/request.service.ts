import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

// Define an interface for the request data for type safety
export interface AdvisoryRequest {
  id?: number;
  nombres: string;
  correo:string;
  idiomas: string[];
  contactado: boolean;
  fecha_creacion: string;
  fecha_contacto: string;
}

// Define an interface for the detailed request data
export interface RequestDetail {
  id: number;
  nombre: string;
  email: string;
  telefono: string;
  idiomas: string[];
  estado_contacto: boolean;
  fecha_creacion: string;
}

// Define an interface for the filter parameters for clarity
export interface RequestFilters {
  nombres?: string;
  correo?: string;
  idiomas?: string[];
  contactado?: boolean;
  fecha_contacto_inicio?: string;
  fecha_contacto_fin?: string;
  fecha_creacion_inicio?: string;
  fecha_creacion_fin?: string;
}

@Injectable({
  providedIn: 'root'
})
export class RequestService {
  private apiUrl = `${environment.API_BASE_URL}/v1/requests`;

  constructor(private http: HttpClient) { }

  /**
   * Fetches advisory requests from the backend, with optional filtering.
   * @param filters An object containing the filter criteria.
   * @returns An Observable containing an array of AdvisoryRequest objects.
   */
  getRequests(filters: RequestFilters = {}): Observable<AdvisoryRequest[]> {
    let params = new HttpParams();

    // Iterate over the filters and append them to HttpParams if they are defined and not null
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        if (Array.isArray(value)) {
          // For arrays like 'idiomas', append each value separately
          value.forEach(item => {
            params = params.append(`${key}[]`, item); // PHP expects array format as 'key[]'
          });
        } else {
          params = params.append(key, value.toString());
        }
      }
    });

    return this.http.get<AdvisoryRequest[]>(this.apiUrl, { params });
  }

  /**
   * Fetches the detailed information for a single advisory request.
   * @param id The ID of the request to fetch.
   * @returns An Observable containing the detailed request data.
   */
  getRequestDetails(id: number): Observable<RequestDetail> {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get<RequestDetail>(url);
  }

  /**
   * Updates the contact status of a specific advisory request.
   * @param id The ID of the request to update.
   * @param contactado The new contact status.
   * @returns An Observable with the result of the operation.
   */
  updateContactStatus(id: number, contactado: boolean): Observable<any> {
    const url = `${this.apiUrl}/${id}/status`;
    const body = { contactado };
    return this.http.put(url, body);
  }
}