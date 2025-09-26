import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { map, take } from 'rxjs/operators';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  // Usamos `checkSession` para validar la sesión con el backend.
  return authService.checkSession().pipe(
    take(1), // Tomamos el primer valor emitido y nos desuscribimos.
    map(isAuthenticated => {
      if (isAuthenticated) {
        return true; // Si está autenticado, permite el acceso.
      } else {
        // Si no está autenticado, redirige a la página de login.
        return router.createUrlTree(['/login']);
      }
    })
  );
};