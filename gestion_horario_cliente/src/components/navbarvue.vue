<template>
  <nav class="flex items-center justify-between bg-white border border-gray-200 dark:border-gray-700 px-2 sm:px-4 py-2.5 rounded dark:bg-gray-800 shadow w-full">
    <div class="w-full">
      <button @click="toggleMobileMenu" class="md:hidden text-gray-700 dark:text-gray-400 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>
      <ul :class="[
        'md:flex',
        'flex-col',
        'md:flex-row',
        'w-full',
        'justify-between',
        'space-x-0',
        'md:space-x-4',
        'mt-4',
        'md:mt-0',
        'md:text-sm',
        'md:font-medium',
        { 'flex': isMobileMenuOpen, 'hidden': !isMobileMenuOpen }
      ]" id="mobile-menu">
        <li>
          <a href="../horario" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Mi horario
          </a>
        </li>
        <li>
          <a href="../misFaltas" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Mis faltas
          </a>
        </li>
        <li>
          <a href="../guardiasHoy" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Tabla de guardias
          </a>
        </li>
        <li v-if="isAdmin">
          <a href="../faltasHoy" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Faltas de hoy
          </a>
        </li>
        <li v-if="isAdmin">
          <a href="../plantillaDocente" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Plantilla docente
          </a>
        </li>
        <li>
          <a href="../miCuenta" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Mi cuenta
          </a>
        </li>
        <li>
          <a @click.prevent="logout" class="block py-2 pr-4 pl-3 text-gray-700 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
            Cerrar sesión
          </a>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      isMobileMenuOpen: false,
      isDropdownMenuOpen: false,
      userRoles: [], // Array para almacenar los 
    };
  },
  computed: {
    isAdmin() {
      // Verifica si el usuario es 'admin'
      return this.userRoles.includes('admin');
    },
  },
  methods: {
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    toggleDropdownMenu() {
      this.isDropdownMenuOpen = !this.isDropdownMenuOpen;
    },
    closeDropdownMenu(event) {
      if (this.isDropdownMenuOpen && !event.target.closest('#dropdown-menu') && !event.target.closest('#mi-cuenta-link')) {
        this.isDropdownMenuOpen = false;
      }
    },
    logout() {
      if (typeof window !== 'undefined') {
        window.sessionStorage.removeItem('authToken');
        window.location.href = 'http://localhost:4321/'; // Redirige a la página de inicio de sesión
      }
    },
    async fetchUserRoles() {
      try {
        const token = window.sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No auth token found');
        }

        const response = await fetch('http://127.0.0.1:8080/api/user/roles', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        this.userRoles = data.roles;
      } catch (error) {
        console.error('Error fetching user roles:', error);
      }
    },
  },
  mounted() {
    document.addEventListener('click', this.closeDropdownMenu);
    this.fetchUserRoles(); // Obtener los roles del usuario cuando el componente se monte
  },
  beforeDestroy() {
    document.removeEventListener('click', this.closeDropdownMenu);
  },
};
</script>

<style scoped>
button {
  color: aliceblue;
}

label {
  color: aliceblue;
}

input {
  color: aliceblue;
}
</style>
