<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-medium mb-4">{{ title }}</h1>
    <h2 class="text-xl mb-8">{{ subtitle }}</h2>
    <div class="datos">
      <input type="text" v-model="username" placeholder="Nombre de usuario o email">
      <input type="password" v-model="password" placeholder="Contraseña">
      <button @click="login">Iniciar sesión</button>
      <a href="#">¿Olvidaste tu contraseña?</a>
      <p v-if="errorMsg">{{ errorMsg }}</p>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const title = ref('Clockwise');
    const subtitle = ref('Organiza tu trabajo');
    const username = ref('');
    const password = ref('');
    const errorMsg = ref('');

    const login = async () => {
      // Primero obtén el CSRF token
      try {
        await axios.get('http://127.0.0.1:8080/sanctum/csrf-cookie');

        // Luego realiza la petición de login
        const response = await axios.post('http://127.0.0.1:8080/api/login', {
          email: username.value,
          password: password.value,
        });

        // Guardar token en local storage o manejar de otra forma
        console.log(response.data);
        localStorage.setItem('access_token', response.data.token);
      } catch (error) {
        errorMsg.value = 'Error al iniciar sesión: ' + error.response.data.message;
        console.error(error);
      }
    }

    return {
      title,
      subtitle,
      username,
      password,
      login,
      errorMsg
    }
  }
}
</script>
