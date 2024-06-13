<!-- <template>
    <div class="bg-gray-200 p-8 rounded-lg shadow-md">
      <h1 class="text-3xl font-medium mb-4 text-center">Restablecer Contraseña</h1>
      <p class="text-center mb-4">Ingresa tu correo electrónico para recibir un enlace de restablecimiento de contraseña.</p>
      <input type="email" v-model="email" placeholder="Correo electrónico" class="block w-full mb-4 p-2 border rounded">
      <button @click="sendResetLink" class="block w-full mb-4 p-2 bg-blue-500 text-white rounded">Enviar enlace</button>
      <p v-if="message" class="text-green-500">{{ message }}</p>
      <p v-if="errorMsg" class="text-red-500">{{ errorMsg }}</p>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import { sendResetPasswordLink } from '../utils/auth';
  
  export default {
    setup() {
      const email = ref('');
      const message = ref('');
      const errorMsg = ref('');
  
      const sendResetLink = async () => {
        try {
          await sendResetPasswordLink(email.value);
          message.value = 'Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.';
          errorMsg.value = '';
        } catch (error) {
          errorMsg.value = 'Error al enviar el enlace de restablecimiento: ' + error.message;
          message.value = '';
        }
      };
  
      return {
        email,
        sendResetLink,
        message,
        errorMsg,
      };
    },
  };
  </script>
   -->