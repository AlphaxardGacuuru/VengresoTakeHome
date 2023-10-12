import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from "@angular/common/http"
import { Observable } from "rxjs"

import { DivCount } from "../DivCount"

  const httpOptions = {
	headers: new HttpHeaders({
		"Content-Type": "application/json"
	})
  }

@Injectable({
  providedIn: 'root'
})
export class DashboardService {

  constructor(private http: HttpClient) { }

  private apiUrl = "http://localhost:8000/api/div-counts"

  getDivCounts(): Observable<DivCount[]> {
	return this.http.get<DivCount[]>(this.apiUrl);
  }
}
