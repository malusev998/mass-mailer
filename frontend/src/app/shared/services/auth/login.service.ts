import { Injectable } from '@angular/core';
import { Login } from 'src/app/auth/store/actions';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Error } from '../../error.model';


export class LoginSuccess {
  user: { name: string; surname: string; email: string; role: string };
  token: { token: string; refreshToken: string; authType: string };
}

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  constructor(private readonly httpService: HttpClient) {}

  public login(
    login: Login
  ): Observable<LoginSuccess | Error> {
    return this.httpService
      .post<LoginSuccess>('/auth/login', login)
      .pipe(
        catchError((error)=> {
          const newError: Error  = {message: error.body.message, statusCode: error.status};
          return of(newError);
        })
      );
  }
}
