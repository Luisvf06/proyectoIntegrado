// src/utils/auth.js

export function getAuthenticatedUser() {
    const token = sessionStorage.getItem('authToken');
    if (!token) {
      return null;
    }
  
    const apiUrl = 'http://127.0.0.1:8000/api/user';
    const headers = {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    };
  
    return fetch(apiUrl, { headers })
      .then(response => response.json())
      .then(user => user)
      .catch(err => null);
  }
  
  export async function loginUser(username, password) {
    const response = await fetch('http://127.0.0.1:8080/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ user_name: username, password: password }),
    });
  
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to login');
    }
  
    return await response.json();
  }
  