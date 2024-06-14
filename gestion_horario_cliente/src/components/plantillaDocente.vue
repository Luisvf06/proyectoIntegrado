<template>
  <div class="container mx-auto flex flex-wrap py-4">
    <!-- Formulario para cargar archivos XML -->
    <div class="w-full md:w-1/2 flex flex-col items-start">
      <form @submit.prevent="handleFileUpload" class="mb-4">
        <label for="file" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Adjunte el documento .xml:</label>
        <div class="flex">
          <input type="file" id="file" name="file" @change="onFileChange" class="block w-2/3 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          <button type="submit" :disabled="isLoading" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 flex items-center">
            <span v-if="isLoading" class="mr-2">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? "Por favor, espere..." : "Cargar Archivo" }}
          </button>
        </div>
      </form>
    </div>

    <!-- Búsqueda de docentes -->
    <div class="w-full md:w-1/2 flex flex-col items-start">
      <label for="search" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Buscar Docente:</label>
      <input type="text" id="search" v-model="searchTerm" @input="searchUsers" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
      <ul v-if="filteredUsers.length" class="mt-2 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <li v-for="user in filteredUsers" :key="user.id" class="p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200" @click="selectProfessor(user.id)">
          {{ user.name }}
        </li>
      </ul>
    </div>

    <!-- Card de usuario seleccionado -->
    <div v-if="selectedProfessor" class="w-full mt-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4" v-if="selectedUser">
        <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">{{ selectedUser.name }}</h2>
        <div class="flex space-x-4 mb-4">
          <button @click="isEditVisible = true" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-700">Editar Usuario</button>
          <button @click="confirmDeleteProfessor" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-700">Eliminar Usuario</button>
          <button @click="showFaltas" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Ver Faltas</button>
          <button @click="showHorario" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700">Ver Horario</button>
        </div>

        <!-- Formulario de edición -->
        <div v-if="isEditVisible" class="mt-4">
          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Nombre:</label>
          <input v-model="selectedUser.name" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          
          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Username:</label>
          <input v-model="selectedUser.user_name" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          
          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Email:</label>
          <input v-model="selectedUser.email" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña:</label>
          <input v-model="selectedUser.password" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Código de Profesor:</label>
          <input v-model="selectedUser.professor_cod" type="text" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          
          <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Rol:</label>
          <select v-model="selectedUser.roles[0].name" class="block w-full mb-4 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
          </select>

          <button @click="updateProfessor" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Guardar Cambios</button>
        </div>
      </div>
    </div>

    <!-- Tabla de ausencias -->
    <div v-if="isFaltasVisible" class="w-full mt-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Ausencias del Profesor</h3>
        <div v-if="loading" class="text-center py-4">Cargando ausencias...</div>
        <div v-if="!loading && ausencias.length === 0" class="text-center py-4">No hay ausencias registradas</div>
        <div v-if="!loading && ausencias.length > 0" class="w-full">
          <table class="min-w-full bg-gray-800 text-white mt-4">
            <thead class="bg-gray-900 text-white">
              <tr>
                <th class="w-1/2 px-4 py-2">Fecha</th>
                <th class="w-1/2 px-4 py-2">Hora</th>
                <th class="w-1/2 px-4 py-2">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ausencia in ausencias" :key="ausencia.id">
                <td class="border px-4 py-2">{{ ausencia.fecha }}</td>
                <td class="border px-4 py-2">{{ ausencia.hora }}</td>
                <td class="border px-4 py-2 flex flex-col space-y-2">
                  <button @click="editAusencia(ausencia)" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Editar</button>
                  <button @click="confirmEliminarAusencia(ausencia.id)" class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Contenedor para el horario -->
    <div v-if="isHorarioVisible" ref="scheduleContainer" class="w-full mt-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
      <!-- Aquí se renderizará el horario -->
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      roles: [],
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
        role_id: '',
        password: '',
        email: '',
      },
      filteredUsers: [],
      isLoading: false,
      selectedUser: null,
      loading: false,
      horarios: [],
      franjas: []
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
        this.isLoading = true;
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
      } finally {
        this.isLoading = false;
      }
    },
    async fetchUsers() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const response = await fetch('http://127.0.0.1:8080/api/users', {
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

        const users = await response.json();
        this.users = users;
      } catch (error) {
        console.error('Error al obtener los usuarios:', error.message);
        alert(`Error al obtener los usuarios: ${error.message}`);
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
    filterUsers() {
      const searchTerm = this.searchTerm.toLowerCase();
      this.filteredUsers = this.users.filter(user => user.name.toLowerCase().includes(searchTerm));
    },
    async searchUsers() {
      if (this.searchTerm.length > 2) {
        await this.fetchUsers();
        this.filterUsers();
      } else {
        this.filteredUsers = [];
      }
    },
    selectProfessor(professorId) {
      this.selectedProfessor = professorId;
      this.selectedUser = this.users.find(user => user.id === professorId);
      this.searchTerm = '';
      this.filteredUsers = [];
    },
    async updateProfessor() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const updatedUser = {
          ...this.selectedUser,
          roles: [this.selectedUser.roles[0].name]
        };

        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedUser.id}`, {
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
        this.isEditVisible = false;
      } catch (error) {
        console.error('Error al actualizar el usuario:', error.message);
        alert(`Error al actualizar el usuario: ${error.message}`);
      }
    },
    async deleteProfessor() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          console.error('No se encontró el token de autenticación.');
          alert('No se encontró el token de autenticación. Por favor, inicie sesión.');
          return;
        }

        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedUser.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        alert('Usuario eliminado correctamente.');
        this.selectedUser = null;
        this.selectedProfessor = '';
        this.fetchUsers();
      } catch (error) {
        console.error('Error al eliminar el usuario:', error.message);
        alert(`Error al eliminar el usuario: ${error.message}`);
      }
    },
    confirmDeleteProfessor() {
      if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        this.deleteProfessor();
      }
    },
    async fetchAusencias() {
      this.loading = true;
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        const response = await fetch(`http://127.0.0.1:8080/api/users/${this.selectedProfessor}/ausencias-with-details`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const data = await response.json();
        this.ausencias = data;
      } catch (error) {
        console.error('Error al obtener las ausencias:', error.message);
        alert(`Error al obtener las ausencias: ${error.message}`);
      } finally {
        this.loading = false;
      }
    },
    showFaltas() {
      this.fetchAusencias();
      this.isFaltasVisible = true;
    },
    async fetchHorario() {
      this.loading = true;
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        const response = await fetch(`http://127.0.0.1:8080/api/horario/user-details/${this.selectedProfessor}`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Error en la respuesta del servidor: ${response.statusText} - ${errorText}`);
        }

        const data = await response.json();
        this.horarios = data.horarios;
        this.franjas = data.franjas;
        this.isHorarioVisible = true;
        this.$nextTick(() => {
          this.renderSchedule();
        });
      } catch (error) {
        console.error('Error al obtener el horario:', error.message);
        alert('Error al obtener el horario');
      } finally {
        this.loading = false;
      }
    },
    showHorario() {
      this.fetchHorario();
    },
    renderSchedule() {
      const scheduleContainer = this.$refs.scheduleContainer;

      if (!scheduleContainer) {
        console.error('Schedule container not found');
        return;
      }

      const table = document.createElement('table');
      table.className = 'min-w-full border-collapse border border-gray-200 text-white';

      const thead = document.createElement('thead');
      const headerRow = document.createElement('tr');
      const headers = ['Horas', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
      headers.forEach(headerText => {
        const th = document.createElement('th');
        th.className = 'border border-gray-200 px-4 py-2';
        th.textContent = headerText;
        headerRow.appendChild(th);
      });
      thead.appendChild(headerRow);
      table.appendChild(thead);

      const tbody = document.createElement('tbody');

      const franjasHorarios = this.franjas.reduce((acc, franja) => {
        const hora = `${franja.hora_desde.slice(0, 5)} - ${franja.hora_hasta.slice(0, 5)}`;
        acc[hora] = { ...acc[hora], horaDesde: franja.hora_desde, horaHasta: franja.hora_hasta };
        return acc;
      }, {});

      this.horarios.forEach(horario => {
        const franja = this.franjas.find(f => f.id === horario.franja_id);
        const hora = `${franja.hora_desde.slice(0, 5)} - ${franja.hora_hasta.slice(0, 5)}`;
        if (!franjasHorarios[hora][horario.dia]) {
          franjasHorarios[hora][horario.dia] = [];
        }
        franjasHorarios[hora][horario.dia].push(horario);
      });

      Object.keys(franjasHorarios).forEach(hora => {
        const row = document.createElement('tr');

        const horaCell = document.createElement('td');
        horaCell.className = 'border border-gray-200 px-4 py-2 text-center';
        const [horaDesde, horaHasta] = hora.split(' - ');
        horaCell.innerHTML = `<div style="display: flex; flex-direction: column; align-items: center;">
                                <span>${horaDesde}</span>
                                <span>-</span>
                                <span>${horaHasta}</span>
                              </div>`;
        row.appendChild(horaCell);

        const days = ['L', 'M', 'X', 'J', 'V'];
        days.forEach(day => {
          const dayCell = document.createElement('td');
          dayCell.className = 'border border-gray-200 px-4 py-2';
          if (franjasHorarios[hora][day]) {
            const asignaturas = franjasHorarios[hora][day].map(horario => {
              const asignaturaDescripcion = horario.asignatura.descripcion || 'No hay datos';
              const aulaDescripcion = horario.aula ? horario.aula.descripcion : 'No hay datos';
              return `${asignaturaDescripcion} (${aulaDescripcion})`;
            }).join(', ');
            dayCell.textContent = asignaturas;
          }
          row.appendChild(dayCell);
        });

        tbody.appendChild(row);
      });

      table.appendChild(tbody);
      scheduleContainer.innerHTML = '';
      scheduleContainer.appendChild(table);
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
    this.fetchUsers();
    this.fetchRoles();
  }
}
</script>

<style scoped>
button { color: aliceblue; }
label { color: aliceblue; }
input, select { color: aliceblue; }
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
ul li {
  color: var(--color-text, #ffffff);
}
</style>
