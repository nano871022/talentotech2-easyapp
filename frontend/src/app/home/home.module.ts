import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { HomeRoutingModule } from './home-routing.module';
import { LandingPageComponent } from './components/landing-page/landing-page.component';
import { RegisterPageComponent } from './components/register-page/register-page.component';


@NgModule({
  declarations: [
    LandingPageComponent,
    RegisterPageComponent
  ],
  imports: [
    CommonModule,
    HomeRoutingModule,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class HomeModule { }