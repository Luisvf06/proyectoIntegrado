<template>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-medium mb-4">{{ title }}</h1>
      <h2 class="text-xl mb-8">{{ subtitle }}</h2>
      <div class="datos">
        <input type="text" v-model="username" placeholder="Nombre de usuario o email">
        <input type="password" v-model="password" placeholder="Contraseña">
        <button @click="login">Iniciar sesión</button>
        <a href="#">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  </template>
  
  <script>
import { ref } from 'vue'
import axios from 'axios';

export default {
  setup() {
    const title = ref('Clockwise')
    const subtitle = ref('Organiza tu trabajo')
    const username = ref('')
    const password = ref('')

    const login = async () => {
        
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
            username: username.value,
            email: username.value,
          password: password.value
        });

        // Handle successful login
        console.log(response.data);
      } catch (error) {
        // Handle error
        console.error(error);
      }
    }

    return {
      title,
      subtitle,
      username,
      password,
      login,
    }
  }
}
</script>
