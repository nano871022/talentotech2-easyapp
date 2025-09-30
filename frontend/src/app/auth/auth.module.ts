import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms'; // Import forms modules

import { AuthRoutingModule } from './auth-routing.module';
import { AuthLayoutComponent } from './components/auth-layout/auth-layout.component';
import { LoginComponent } from './components/login/login.component'; // Import LoginComponent

@NgModule({
  declarations: [
    AuthLayoutComponent,
    LoginComponent // Declare LoginComponent
  ],
  imports: [
    CommonModule,
    AuthRoutingModule,
    FormsModule, // Add forms modules here
    ReactiveFormsModule
  ]
})
export class AuthModule { }