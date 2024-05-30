<template>
  <div>
    <div class="mt-1.5 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
      <a href="/crearAusencia" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Crear Ausencia</a>
    </div>

    <div id="ausencias-container" class="mt-4">
      <div class="text-center py-4">Cargando ausencias...</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MisAusencias',
  data() {
    return {
      ausencias: [],
      editMode: {},
    };
  },
  async mounted() {
    await this.fetchAusencias();
  },
  methods: {
    async fetchAusencias() {
      const container = document.getElementById('ausencias-container');
      try {
        const ausencias = await this.getAusencias();
        this.ausencias = ausencias;
        if (ausencias.length === 0) {
          container.innerHTML = '<div class="text-center py-4">No hay ausencias registradas</div>';
        } else {
          this.renderTable();
        }
      } catch (error) {
        container.innerHTML = `<div class="text-center py-4 text-red-500">Error: ${error.message}</div>`;
      }
    },
    async getAusencias() {
      try {
        const token = sessionStorage.getItem('authToken');
        const response = await fetch('http://127.0.0.1:8080/api/ausencias', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        if (!response.ok) {
          throw new Error(`Failed to fetch ausencias: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        return data;
      } catch (err) {
        console.error('Error fetching ausencias:', err);
        return [];
      }
    },
    renderTable() {
      const container = document.getElementById('ausencias-container');
      const table = document.createElement('table');
      table.className = 'min-w-full bg-white mt-4';

      const thead = document.createElement('thead');
      thead.className = 'bg-gray-800 text-white';
      thead.innerHTML = `
        <tr>
          <th class="w-1/4 px-4 py-2">Fecha</th>
          <th class="w-1/4 px-4 py-2">Hora</th>
          <th class="w-1/4 px-4 py-2">ID</th>
          <th class="w-1/4 px-4 py-2">Acciones</th>
        </tr>
      `;
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      this.ausencias.forEach(ausencia => {
        const tr = document.createElement('tr');
        tr.innerHTML = this.editMode[ausencia.id] ? this.getEditableRow(ausencia) : this.getReadOnlyRow(ausencia);
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);

      container.innerHTML = '';
      container.appendChild(table);

      this.addEventListeners();
    },
    getReadOnlyRow(ausencia) {
      return `
        <td class="border px-4 py-2">${ausencia.fecha}</td>
        <td class="border px-4 py-2">${ausencia.hora}</td>
        <td class="border px-4 py-2">${ausencia.id}</td>
        <td class="border px-4 py-2 flex flex-col space-y-2">
          <button class="editar-btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900" data-id="${ausencia.id}">Editar</button>
          <button class="eliminar-btn text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900" data-id="${ausencia.id}">Eliminar</button>
        </td>
      `;
    },
    getEditableRow(ausencia) {
      return `
        <td class="border px-4 py-2">
          <input type="date" v-model="ausencia.fecha">
        </td>
        <td class="border px-4 py-2"><input type="time" v-model="ausencia.hora"></td>
        <td class="border px-4 py-2">${ausencia.id}</td>
        <td class="border px-4 py-2 flex flex-col space-y-2">
          <button class="guardar-btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800" data-id="${ausencia.id}">Guardar</button>
          <button class="cancelar-btn text-gray-700 hover:text-white border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" data-id="${ausencia.id}">Cancelar</button>
        </td>
      `;
    },
    addEventListeners() {
      document.querySelectorAll('.editar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          this.editMode = { ...this.editMode, [id]: true };
          this.renderTable();
        });
      });

      document.querySelectorAll('.eliminar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          this.eliminarAusencia(id);
        });
      });

      document.querySelectorAll('.guardar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          this.guardarAusencia(id);
        });
      });

      document.querySelectorAll('.cancelar-btn').forEach(button => {
        button.addEventListener('click', (event) => {
          const id = event.currentTarget.getAttribute('data-id');
          this.editMode = { ...this.editMode, [id]: false };
          this.renderTable();
        });
      });
    },
    async guardarAusencia(id) {
      const row = document.querySelector(`button[data-id="${id}"]`).closest('tr');
      const fechaInput = row.querySelector('input[type="date"]').value;
      const horaInput = row.querySelector('input[type="time"]').value;

      // Formatear la fecha al formato d/m/y
      const date = new Date(fechaInput);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses empiezan en 0
      const year = date.getFullYear();
      const formattedDate = `${day}/${month}/${year}`;

      // Validar que la fecha no sea anterior a la fecha actual
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (date < today) {
        console.error('Fecha inválida: la fecha no puede ser anterior a la fecha actual');
        alert('Fecha inválida. La fecha no puede ser anterior a la fecha actual.');
        return;
      }

      try {
        const token = sessionStorage.getItem('authToken');
        const response = await fetch(`http://127.0.0.1:8080/api/ausencias/${id}`, {
          method: 'PATCH', // Cambiado a PATCH
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            fecha: formattedDate,
            hora: horaInput,
          })
        });

        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Failed to update ausencia: ${response.status} ${response.statusText}. Details: ${errorText}`);
        }

        // Actualizar el estado local sin recargar toda la tabla
        const updatedAusencia = await response.json();
        const index = this.ausencias.findIndex(a => a.id === id);
        if (index !== -1) {
          this.$set(this.ausencias, index, updatedAusencia.data);
        }

        this.editMode = { ...this.editMode, [id]: false };
      } catch (err) {
        console.error('Error updating ausencia:', err);
      }
    },
    async eliminarAusencia(id) {
      if (!id) {
        console.error('No id provided for deletion');
        return;
      }
      const token = sessionStorage.getItem('authToken');
      fetch(`http://127.0.0.1:8080/api/ausencias/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
      .then(async response => {
        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Network response was not ok: ${response.statusText}. Details: ${errorText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Ausencia eliminada:', data);
        alert('Ausencia eliminada correctamente');

        this.ausencias = this.ausencias.filter(a => a.id !== id);
      })
      .catch(error => {
        console.error('Error eliminando la ausencia:', error);
        alert(`Error eliminando la ausencia: ${error.message}`);
      });
    }
  }
}
</script>

<style scoped>

</style>
