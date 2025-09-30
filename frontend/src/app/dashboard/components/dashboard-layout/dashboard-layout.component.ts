import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { RequestService, AdvisoryRequest } from '../../../core/services/request.service';

@Component({
  selector: 'app-dashboard-layout',
  templateUrl: './dashboard-layout.component.html',
  styleUrls: ['./dashboard-layout.component.scss']
})
export class DashboardLayoutComponent implements OnInit {
  // Use an observable to handle the data stream
  requests$!: Observable<AdvisoryRequest[]>;
  error: string | null = null;

  // Placeholder for admin name, will be set after login implementation
  adminName = 'Admin';

  constructor(private requestService: RequestService) {}

  ngOnInit(): void {
    this.fetchRequests();
  }

  fetchRequests(): void {
    this.requests$ = this.requestService.getRequests().pipe(
      catchError(err => {
        console.error('Failed to fetch requests', err);
        this.error = 'No se pudieron cargar las solicitudes. Por favor, inténtelo de nuevo más tarde.';
        // Return an empty array to clear the view on error
        return of([]);
      })
    );
  }

  // Placeholder for logout functionality
  logout(): void {
    console.log('Logging out...');
  }
}