<template>
  <div>
    <div id="faltas-container" class="mt-4">
      <div class="text-center py-4 text-white">Cargando faltas...</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FaltasHoy',
  data() {
    return {
      faltas: []
    };
  },
  async mounted() {
    await this.fetchFaltas();
  },
  methods: {
    async fetchFaltas() {
      const container = document.getElementById('faltas-container');
      try {
        const faltas = await this.getFaltas();
        console.log('Datos almacenados en el estado:', faltas); 
        this.faltas = faltas;
        if (faltas.length === 0) {
          container.innerHTML = '<div class="text-center py-4 text-white">No hay faltas registradas para hoy</div>';
        } else {
          this.renderTable();
        }
      } catch (error) {
        container.innerHTML = `<div class="text-center py-4 text-red-500">Error: ${error.message}</div>`;
      }
    },
    async getFaltas() {
      try {
        const token = sessionStorage.getItem('authToken');
        if (!token) {
          throw new Error('No se encontró el token de autenticación');
        }

        const response = await fetch('http://127.0.0.1:8080/api/ausenciasHoy', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        if (!response.ok) {
          throw new Error(`Failed to fetch faltas: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        console.log('Datos recibidos de la API:', data);
        return data;
      } catch (err) {
        console.error('Error fetching faltas:', err);
        return [];
      }
    },
    renderTable() {
      const container = document.getElementById('faltas-container');
      const table = document.createElement('table');
      table.className = 'min-w-full bg-white mt-4';

      const thead = document.createElement('thead');
      thead.className = 'bg-gray-800 text-white';
      thead.innerHTML = `
        <tr>
          <th class="w-1/6 px-4 py-2">Nombre del Usuario</th>
          <th class="w-1/6 px-4 py-2">Fecha</th>
          <th class="w-1/6 px-4 py-2">Hora</th>
          <th class="w-1/6 px-4 py-2">ID</th>
          <th class="w-1/6 px-4 py-2">Aula</th>
          <th class="w-1/6 px-4 py-2">Grupo</th>
        </tr>
      `;
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      this.faltas.forEach(falta => {
        console.log('Renderizando fila:', falta);
        const tr = document.createElement('tr');
        tr.innerHTML = this.getReadOnlyRow(falta);
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);

      container.innerHTML = '';
      container.appendChild(table);
    },
    getReadOnlyRow(falta) {
      const fechaFormateada = new Date(falta.fecha).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });

      const hora = falta.hora ? falta.hora : 'todo el día';

      return `
        <td class="border px-4 py-2 text-black">${falta.user_name}</td>
        <td class="border px-4 py-2 text-black">${fechaFormateada}</td>
        <td class="border px-4 py-2 text-black">${hora}</td>
        <td class="border px-4 py-2 text-black">${falta.id}</td>
        <td class="border px-4 py-2 text-black">${falta.aula_descripcion}</td>
        <td class="border px-4 py-2 text-black">${falta.grupo_descripcion}</td>
      `;
    }
  }
}
</script>

<style scoped>
.text-white {
  color: white;
}
.text-black {
  color: black;
}
</style>
