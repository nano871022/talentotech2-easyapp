import { Routes } from '@angular/router';
import { RegisterComponent } from './components/register/register.component';
import { LoginComponent } from './components/login/login.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { authGuard } from './guards/auth.guard';
import { LandingPageComponent } from './landing-page/landing-page';

export const routes: Routes = [
  {
    path: '',
    component: LandingPageComponent,
    title: 'Language Advisory Services'
  },
  {
    path: 'register',
    component: RegisterComponent,
    title: 'Solicitud de Asesoría'
  },
  {
    path: 'login',
    component: LoginComponent,
    title: 'Acceso de Administrador'
  },
  {
    path: 'dashboard',
    component: DashboardComponent,
    canActivate: [authGuard],
    title: 'Panel de Administración'
  },
  // Redirigir cualquier ruta no encontrada a la página de inicio.
  {
    path: '**',
    redirectTo: '',
    pathMatch: 'full'
  }
];
