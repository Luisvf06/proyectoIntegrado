<template>
  <nav class="bg-white dark:bg-gray-800 shadow w-full">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <button @click="toggleMobileMenu" class="md:hidden text-gray-700 dark:text-gray-400 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>
        </div>
        <div class="flex-1 flex items-center justify-between">
          <ul class="hidden md:flex space-x-4 w-full justify-evenly">
            <!-- Enlaces de navegación -->
            <li>
              <a href="../horario" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Mi horario
              </a>
            </li>
            <li>
              <a href="../misFaltas" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Mis faltas
              </a>
            </li>
            <li>
              <a href="../guardiasHoy" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Guardias de hoy
              </a>
            </li>
            <li>
              <a href="../faltasHoy" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Faltas de hoy
              </a>
            </li>
            <li>
              <a href="../horarioProfesorado" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Horario del profesorado
              </a>
            </li>
            <li>
              <a href="../plantillaDocente" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
                Modificar plantilla docente
              </a>
            </li>
            <li class="relative">
              <span @click="toggleDropdownMenu" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 cursor-pointer">
                Mi cuenta
              </span>
              <ul v-if="isDropdownVisible" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg dark:bg-gray-800">
                <li>
                  <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    Ajustes
                  </a>
                </li>
                <li>
                  <a @click="logout" href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                    Cerrar sesión
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div v-if="isMobileMenuVisible" class="md:hidden">
      <ul class="space-y-1 px-2 pt-2 pb-3">
        <!-- Enlaces de navegación -->
        <li>
          <a href="../horario" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Mi horario
          </a>
        </li>
        <li>
          <a href="../misFaltas" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Mis faltas
          </a>
        </li>
        <li>
          <a href="../guardiasHoy" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Guardias de hoy
          </a>
        </li>
        <li>
          <a href="../faltasHoy" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Faltas de hoy
          </a>
        </li>
        <li>
          <a href="../horarioProfesorado" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Horario del profesorado
          </a>
        </li>
        <li>
          <a href="../plantillaDocente" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700">
            Modificar plantilla docente
          </a>
        </li>
        <li class="relative">
          <span @click="toggleDropdownMenu" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 cursor-pointer">
            Mi cuenta
          </span>
          <ul v-if="isDropdownVisible" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg dark:bg-gray-800">
            <li>
              <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                Ajustes
              </a>
            </li>
            <li>
              <a @click="logout" href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                Cerrar sesión
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      isDropdownVisible: false,
      isMobileMenuVisible: false,
    };
  },
  methods: {
    toggleDropdownMenu(event) {
      event.stopPropagation();
      this.isDropdownVisible = !this.isDropdownVisible;
    },
    toggleMobileMenu() {
      this.isMobileMenuVisible = !this.isMobileMenuVisible;
    },
    logout(event) {
      event.preventDefault();
      if (typeof window !== 'undefined') {
        window.localStorage.removeItem('access_token');
        window.location.href = 'http://localhost:4321';
      }
    },
  },
  mounted() {
    document.addEventListener('click', this.closeDropdownMenu);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.closeDropdownMenu);
  },
  methods: {
    closeDropdownMenu(event) {
      if (this.isDropdownVisible) {
        this.isDropdownVisible = false;
      }
    },
  }
};
</script>

<style scoped>
/* Aquí puedes añadir estilos específicos para este componente */
</style>
