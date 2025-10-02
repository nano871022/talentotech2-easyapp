import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthLayoutComponent } from './components/auth-layout/auth-layout.component';
import { LoginComponent } from './components/login/login.component'; // Import the LoginComponent

const routes: Routes = [
  {
    path: '',
    component: AuthLayoutComponent,
    // Define child routes for login
    children: [
      { path: 'login', component: LoginComponent },
      // Redirect /auth to /auth/login by default
      { path: '', redirectTo: 'login', pathMatch: 'full' }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AuthRoutingModule { }