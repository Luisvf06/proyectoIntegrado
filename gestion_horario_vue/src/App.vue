<template>
  <router-view login/>
</template>

<script>
import { ref, computed } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const title = ref('Clockwise');
    const subtitle = ref('Organiza tu trabajo');
    const user_name = ref('');
    const password = ref('');
    const errorMsg = ref('');

    const isLoggedIn = computed(() => {
      return typeof window !== 'undefined' ? !!window.localStorage.getItem('access_token') : false;
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
        //cambiar la direccion ip a la del servidor
        const response = await axios.post('http://127.0.0.1:8080/api/login', {
          user_name: user_name.value,
          password: password.value,
        });
        console.log('Received login response:', response.data);
        if (typeof window !== 'undefined') {
          window.localStorage.setItem('access_token', response.data.token);
          window.location.href = '/horario';
        }
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
      errorMsg,
      isLoggedIn
    }
  }
}
</script>
