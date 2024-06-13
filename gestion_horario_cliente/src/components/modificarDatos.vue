<template>
    <div class="max-w-lg mx-auto p-6 bg-white shadow-md rounded-lg">
      <h2 class="text-2xl font-semibold mb-4">Modificar Datos del Usuario</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
          <input type="text" v-model="user.name" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
          <input type="email" v-model="user.email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
        </div>
        <div class="mb-4">
          <label for="user_name" class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
          <input type="text" v-model="user.user_name" id="user_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
        </div>
        <div class="mb-4">
          <label for="professor_cod" class="block text-sm font-medium text-gray-700">Código de Profesor</label>
          <input type="text" v-model="user.professor_cod" id="professor_cod" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
        </div>
        <div class="mb-4">
          <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
          <select v-model="selectedRoles" id="roles" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" multiple>
            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        user: {
          name: '',
          email: '',
          user_name: '',
          professor_cod: '',
        },
        roles: [],
        selectedRoles: [],
      };
    },
    mounted() {
      const userId = this.$route.params.id; 
      this.fetchUserData(userId);
      this.fetchRoles();
    },
    methods: {
      async fetchUserData(userId) {
        try {
          const response = await axios.get(`http://127.0.0.1:8080/api/user/${userId}`);
          this.user = response.data;
          this.selectedRoles = response.data.roles.map(role => role.id);
        } catch (error) {
          console.error('Error fetching user data:', error);
        }
      },
      async fetchRoles() {
        try {
          const response = await axios.get('http://127.0.0.1:8080/api/roles'); 
          this.roles = response.data;
        } catch (error) {
          console.error('Error fetching roles:', error);
        }
      },
      async submitForm() {
        const userId = this.$route.params.id;
        try {
          await axios.patch(`http://127.0.0.1:8080/api/user/${userId}`, {
            ...this.user,
            roles: this.selectedRoles,
          });
          alert('Usuario actualizado correctamente');
        } catch (error) {
          console.error('Error updating user:', error);
          alert('Hubo un error al actualizar el usuario');
        }
      },
    },
  };
  </script>
  
  <style scoped>
  </style>
  