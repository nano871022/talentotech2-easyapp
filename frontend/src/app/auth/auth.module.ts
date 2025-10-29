import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { AuthRoutingModule } from './auth-routing.module';
import { AuthLayoutComponent } from './components/auth-layout/auth-layout.component';
import { LoginComponent } from './components/login/login.component';

@NgModule({
  declarations: [],
  imports: [
    CommonModule,
    AuthRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    AuthLayoutComponent,
    LoginComponent
  ]
})
export class AuthModule { }
