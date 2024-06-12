<template>
  <div class="container mx-auto py-4">
    <div v-if="user" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
      <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Editar Perfil</h2>

      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Nombre:</label>
      <input v-model="user.name" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
      
      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Username:</label>
      <input v-model="user.user_name" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
      
      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Email:</label>
      <input v-model="user.email" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña Actual:</label>
      <input v-model="currentPassword" type="password" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Nueva Contraseña:</label>
      <input v-model="newPassword" type="password" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
      <p v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</p>

      <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Código de Profesor:</label>
      <input v-model="user.professor_cod" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
      
      <button @click="updateUser" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Guardar Cambios</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: null,
      roles: [],
      currentPassword: '',
      newPassword: '',
      passwordError: ''
    };
  },
  methods: {
    async fetchUser() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const response = await fetch('http://127.0.0.1:8080/api/user', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const user = await response.json();
        this.user = user;
      } catch (error) {
        console.error('Error al obtener el usuario:', error.message);
        alert(`Error al obtener el usuario: ${error.message}`);
      }
    },
    async fetchRoles() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const response = await fetch('http://127.0.0.1:8080/api/roles', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const roles = await response.json();
        this.roles = roles;
      } catch (error) {
        console.error('Error al obtener los roles:', error.message);
        alert(`Error al obtener los roles: ${error.message}`);
      }
    },
    validatePassword(password) {
      const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,}$/;
      return passwordRegex.test(password);
    },
    async updateUser() {
      if (this.newPassword && !this.validatePassword(this.newPassword)) {
        this.passwordError = 'La contraseña debe tener al menos 7 caracteres, incluyendo 1 número, 1 mayúscula y 1 minúscula.';
        return;
      }

      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const updatedUser = {
          ...this.user,
          password: this.newPassword ? this.newPassword : undefined
        };

        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.user.id}`, {
          method: 'PATCH',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(updatedUser)
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const result = await response.json();
        console.log('Usuario actualizado:', result);
        alert('Usuario actualizado correctamente.');
        this.passwordError = '';
        this.currentPassword = '';
        this.newPassword = '';
      } catch (error) {
        console.error('Error al actualizar el usuario:', error.message);
        alert(`Error al actualizar el usuario: ${error.message}`);
      }
    },
  },
  mounted() {
    this.fetchUser();
    this.fetchRoles();
  }
}
</script>

<style scoped>
button { color: aliceblue; }
label { color: aliceblue; }
input, select { color: aliceblue; }
</style>
