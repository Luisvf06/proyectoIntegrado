<template>
  <div class="bg-gray-200 p-8 rounded-lg shadow-md" v-if="!isLoggedIn">
    <h1 class="text-3xl font-medium mb-4 text-center text-gradient">{{ title }}</h1>
    <h2 class="text-xl mb-8 text-center">{{ subtitle }}</h2>
    <div class="datos">
      <input type="text" v-model="user_name" placeholder="Nombre de usuario o email" class="block w-full mb-4 p-2 border rounded">
      <input type="password" v-model="password" placeholder="Contraseña" class="block w-full mb-4 p-2 border rounded">
      <button @click="login" class="block w-full mb-4 p-2 bg-blue-500 text-white rounded">Iniciar sesión</button>
      <a @click="goToForgotPassword" class="block w-full mb-4 p-2 text-center text-blue-500 hover:underline cursor-pointer">¿Olvidaste tu contraseña?</a>
      <p v-if="errorMsg" class="text-red-500">{{ errorMsg }}</p>
    </div>
  </div>
  <div v-else>
    <p>Redireccionando...</p>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { loginUser } from '../utils/auth';

export default {
  setup() {
    const title = ref('Clockwise');
    const subtitle = ref('Organiza tu trabajo');
    const user_name = ref('');
    const password = ref('');
    const errorMsg = ref('');

    const isLoggedIn = computed(() => {
      return typeof window !== 'undefined' ? !!window.sessionStorage.getItem('authToken') : false;
    });

    if (isLoggedIn.value && typeof window !== 'undefined') {
      window.location.href = '/horario';
    }

    const login = async () => {
      try {
        console.log('Sending login request:', {
          user_name: user_name.value,
          password: password.value,
        });

        const response = await loginUser(user_name.value, password.value);
        console.log('Received login response:', response);

        if (typeof window !== 'undefined') {
          window.sessionStorage.setItem('authToken', response.token);
          window.location.href = '/horario';
        }
      } catch (error) {
        errorMsg.value = 'Error al iniciar sesión: ' + error.message;
        console.error(error);
      }
    };

    const goToForgotPassword = () => {
      window.location.href = '/recuperar-contra'; // Redirige a la página de recuperarion de contraseña
    };

    return {
      title,
      subtitle,
      user_name,
      password,
      login,
      errorMsg,
      isLoggedIn,
      goToForgotPassword, 
    };
  },
};
</script>
