<template>
  <div class="bg-gray-200 p-8 rounded-lg shadow-md">
    <h1 class="text-3xl font-medium mb-4 text-center text-gradient">{{ title }}</h1>
    <h2 class="text-xl mb-8 text-center">{{ subtitle }}</h2>
    <div class="datos">
      <input type="text" v-model="user_name" placeholder="Nombre de usuario o email" class="block w-full mb-4 p-2 border rounded">
      <input type="password" v-model="password" placeholder="Contraseña" class="block w-full mb-4 p-2 border rounded">
      <button @click="login" class="block w-full mb-4 p-2 bg-blue-500 text-white rounded">Iniciar sesión</button>
      <a href="#" class="block w-full mb-4 p-2 text-center text-blue-500 hover:underline">¿Olvidaste tu contraseña?</a>
      <p v-if="errorMsg" class="text-red-500">{{ errorMsg }}</p>
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
    const user_name = ref('');
    const password = ref('');
    const errorMsg = ref('');

    const login = async () => {
  try {
    console.log('Sending login request:', {
      user_name: user_name.value,
      password: password.value,
    });
    const response = await axios.post('http://0.0.0.0:8080/api/login', {
      user_name: user_name.value,
      password: password.value,
    });

    console.log('Received login response:', response.data);
    localStorage.setItem('access_token', response.data.token);
  } catch (error) {
    if (error.response) {
      errorMsg.value = 'Error al iniciar sesión: ' + error.response.data.message;
    } else {
      errorMsg.value = 'Error al iniciar sesión: ' + error.message;
    }
    console.error(error);
  }
}

    return {
      title,
      subtitle,
      user_name,
      password,
      login,
      errorMsg
    }
  }
}
</script>