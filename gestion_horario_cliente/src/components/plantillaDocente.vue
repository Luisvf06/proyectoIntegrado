<template>
  <div class="container mx-auto flex flex-wrap py-4">
    <!-- Formulario para cargar archivos XML -->
    <div class="w-full md:w-1/2 flex flex-col items-start">
      <form @submit.prevent="handleFileUpload" class="mb-4">
        <label for="file" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Adjunte el documento .xml:</label>
        <div class="flex">
          <input type="file" id="file" name="file" @change="onFileChange" class="block w-2/3 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
          <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700 flex items-center">
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
    <!-- (rest of your code) -->

  </div>

  <!-- Formulario de edición -->
  <!-- (rest of your code) -->

  <!-- Faltas -->
  <!-- (rest of your code) -->

  <!-- Horario -->
  <!-- (rest of your code) -->
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
      filteredUsers: [],
      isLoading: false // nuevo estado de carga
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
        this.isLoading = true; // iniciar el estado de carga
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
        this.isLoading = false; // terminar el estado de carga
      }
    },
    async fetchAusencias() {
      // (rest of your code)
    },
    async showHorario() {
      // (rest of your code)
    },
    async editProfessor() {
      // (rest of your code)
    },
    async updateProfessor() {
      // (rest of your code)
    },
    cancelEdit() {
      this.isEditVisible = false;
    },
    async deleteProfessor() {
      // (rest of your code)
    },
    async loadUsers() {
      // (rest of your code)
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
