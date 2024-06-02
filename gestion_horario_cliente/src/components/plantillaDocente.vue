<template>
  <div class="container mx-auto flex flex-wrap py-4">
    <!-- Formulario para cargar archivos XML -->
    <div class="w-full md:w-1/2 flex flex-col items-start">
      <form @submit.prevent="handleFileUpload" class="mb-4">
        <label for="file" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Adjunte el documento .xml:</label>
        <div class="flex">
          <input type="file" id="file" name="file" @change="onFileChange" class="block w-2/3 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700">Cargar Archivo</button>
        </div>
      </form>
    </div>

    <!-- Búsqueda de docentes -->
    <div class="w-full md:w-1/2 flex flex-col items-end">
      <div class="flex items-center relative">
        <input v-model="searchTerm" @input="filterUsers" placeholder="Escriba para buscar..." class="block w-96 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mr-4">
        <button v-if="selectedProfessor" @click="editProfessor" class="edit-button" title="Modificar docente">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" color="white">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
          </svg>
        </button>
        <button v-if="selectedProfessor" @click="deleteProfessor" class="delete-button" title="Eliminar docente">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" color="white">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M18 6l-12 12"/>
            <path d="M6 6l12 12"/>
          </svg>
        </button>
        <ul v-if="searchTerm" class="absolute top-10 left-0 w-96 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg z-10">
          <li v-for="u in filteredUsers" :key="u.id" @click="selectProfessor(u.id)" class="px-4 py-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">{{ u.name }}</li>
        </ul>
      </div>
      <div class="mt-4 flex space-x-4">
        <button v-if="selectedProfessor" @click="fetchAusencias" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Ver Faltas</button>
        <button v-if="selectedProfessor" @click="showHorario" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Ver Horario</button>
      </div>
    </div>
  </div>

  <!-- Formulario de edición -->
  <div v-if="isEditVisible" class="container mx-auto py-4">
    <form @submit.prevent="updateProfessor" class="w-full max-w-lg">
      <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2" for="name">
            Nombre
          </label>
          <input v-model="form.name" class="appearance-none block w-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="name" type="text">
        </div>
      </div>
      <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2" for="user_name">
            Nombre de Usuario
          </label>
          <input v-model="form.user_name" class="appearance-none block w-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="user_name" type="text">
        </div>
      </div>
      <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2" for="professor_cod">
            Código de Profesor
          </label>
          <input v-model="form.professor_cod" class="appearance-none block w-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="professor_cod" type="text">
        </div>
      </div>
      <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3 mb-6 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-200 text-xs font-bold mb-2" for="role_id">
            ID de Rol
          </label>
          <input v-model="form.role_id" class="appearance-none block w-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="role_id" type="text">
        </div>
      </div>
      <div class="flex items-center justify-between">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
          Guardar Cambios
        </button>
        <button @click="cancelEdit" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" type="button">
          Cancelar
        </button>
      </div>
    </form>
  </div>

  <!-- Faltas -->
  <div v-show="isFaltasVisible" class="container mx-auto py-4">
    <table border="1" class="min-w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Fecha</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Hora</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Nombre</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Aula</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Grupo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ausencia in ausencias" :key="ausencia.id">
          <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">{{ ausencia.fecha }}</td>
          <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">{{ ausencia.hora }}</td>
          <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">{{ ausencia.user_name }}</td>
          <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">{{ ausencia.user}}</td>
          <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">{{ ausencia.grupo_descripcion }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Horario -->
  <div v-show="isHorarioVisible" class="container mx-auto py-4">
    <table border="1" class="min-w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Hora</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Asignatura</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Edificio-Aula</th>
          <th class="py-2 px-4 border-b-2 border-gray-200 dark:border-gray-600">Grupo</th>
        </tr>
      </thead>
      <tbody>
        <!-- Necesito los datos para poder hacer las celdas -->
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      ausencias: [],
      searchTerm: '',
      selectedProfessor: '',
      isFaltasVisible: false,
      isHorarioVisible: false,
      isEditVisible: false,
      file: null,
      form: {
        name: '',
        user_name: '',
        professor_cod: '',
        role_id: ''
      },
      filteredUsers: []
    };
  },
  methods: {
    onFileChange(event) {
      this.file = event.target.files[0];
    },
    async handleFileUpload() {
      if (!this.file) {
        alert('Por favor, seleccione un archivo para cargar.');
        return;
      }

      const token = sessionStorage.getItem('authToken');
      if (!token) {
        console.error('No se encontró el token de autenticación.');
        alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
        return;
      }

      const formData = new FormData();
      formData.append('file', this.file);

      try {
        const response = await fetch('http://127.0.0.1:8080/api/upload-xml', {  
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`
          },
          body: formData,
        });

        if (!response.ok) {
          const errorText = await response.text(); 
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const result = await response.json();
        console.log('Archivo procesado:', result);
        alert('Archivo procesado correctamente.');
      } catch (error) {
        console.error('Error al subir el archivo:', error.message);  
        alert(`Error al procesar el archivo: ${error.message}`);
      }
    },
    async fetchAusencias() {
      const token = sessionStorage.getItem('authToken');
      if (!token) {
        console.error('No se encontró el token de autenticación.');
        alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
        return;
      }

      console.log(`Obteniendo ausencias para el profesor con ID: ${this.selectedProfessor}`);

      try {
        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedProfessor}/ausencias`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        if (!response.ok) {
          const errorText = await response.text(); 
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const ausencias = await response.json();
        console.log('Ausencias obtenidas:', ausencias);
        this.ausencias = ausencias;
        this.isFaltasVisible = true;
        this.isHorarioVisible = false;
      } catch (error) {
        console.error('Error al obtener las ausencias:', error.message);  
        alert(`Error al obtener las ausencias: ${error.message}`);
      }
    },
    async showHorario() {
      this.isFaltasVisible = false;
      this.isHorarioVisible = true;
    },
    async editProfessor() {
      const selectedUser = this.users.find(user => user.id === this.selectedProfessor);
      if (selectedUser) {
        try {
          const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedProfessor}`);
          if (!response.ok) {
            throw new Error('Error al obtener el usuario');
          }
          const userData = await response.json();
          this.form = {
            name: userData.name,
            user_name: userData.user_name,
            professor_cod: userData.professor_cod,
            role_id: userData.roles.length > 0 ? userData.roles[0].pivot.role_id : ''
          };
          this.isEditVisible = true;
        } catch (error) {
          console.error('Error al obtener los datos del usuario:', error.message);
          alert(`Error al obtener los datos del usuario: ${error.message}`);
        }
      }
    },
    async updateProfessor() {
      const token = sessionStorage.getItem('authToken');
      if (!token) {
        console.error('No se encontró el token de autenticación.');
        alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
        return;
      }

      try {
        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedProfessor}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.form),
        });

        if (!response.ok) {
          const errorText = await response.text();  
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const result = await response.json();
        console.log('Usuario actualizado:', result);
        alert('Usuario actualizado correctamente.');
        this.isEditVisible = false;
        this.loadUsers(); 
      } catch (error) {
        console.error('Error al actualizar el usuario:', error.message);  
        alert(`Error al actualizar el usuario: ${error.message}`);
      }
    },
    cancelEdit() {
      this.isEditVisible = false;
    },
    async deleteProfessor() {
      const token = sessionStorage.getItem('authToken');
      if (!token) {
        console.error('No se encontró el token de autenticación.');
        alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
        return;
      }

      if (confirm('¿Está seguro de que desea eliminar este profesor?')) {
        try {
          const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedProfessor}`, {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          if (!response.ok) {
            const errorText = await response.text();  
            throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
          }
          alert('Profesor eliminado correctamente.');
          this.selectedProfessor = '';
          this.loadUsers();
        } catch (error) {
          console.error('Error al eliminar el profesor:', error.message);  
          alert(`Error al eliminar el profesor: ${error.message}`);
        }
      }
    },
    async loadUsers() {
      const token = sessionStorage.getItem('authToken');
      if (!token) {
        console.error('No se encontró el token de autenticación.');
        alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
        return;
      }

      console.log('Cargando lista de usuarios...');

      try {
        const response = await fetch('http://127.0.0.1:8080/api/users', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        if (!response.ok) {
          const errorText = await response.text();  
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }
        const data = await response.json();
        console.log('Usuarios cargados:', data);
        this.users = data;
        this.filteredUsers = data;
      } catch (error) {
        console.error('Error al obtener la lista de usuarios:', error.message);  
        alert(`Error al obtener la lista de usuarios: ${error.message}`);
      }
    },
    filterUsers() {
      this.filteredUsers = this.users.filter(user => user.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
    },
    selectProfessor(professorId) {
      this.selectedProfessor = professorId;
      this.searchTerm = '';
      this.filteredUsers = [];
    }
  },
  watch: {
    selectedProfessor() {
      this.isFaltasVisible = false;
      this.isHorarioVisible = false;
      this.isEditVisible = false;
    }
  },
  mounted() {
    this.loadUsers();
  }
}
</script>

<style scoped>
button { color: aliceblue; }
label { color: aliceblue; }
input { color: aliceblue; }
.edit-button, .delete-button {
  background: none;
  border: none;
  cursor: pointer;
}
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
</style>
