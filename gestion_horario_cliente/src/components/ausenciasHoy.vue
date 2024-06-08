<template>
  <div class="container mx-auto">
    <div id="faltas-container" class="mt-4 flex justify-center">
      <div class="text-center py-4 text-white">Cargando faltas...</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FaltasHoy',
  data() {
    return {
      faltas: [],
      diaSemana: ''
    };
  },
  async mounted() {
    this.setDiaSemana();
    await this.fetchFaltas();
  },
  methods: {
    setDiaSemana() {
      const days = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
      const today = new Date();
      this.diaSemana = days[today.getDay()];
    },
    async fetchFaltas() {
      const container = document.getElementById('faltas-container');
      try {
        const faltas = await this.getFaltas();
        console.log('Datos almacenados en el estado:', faltas);
        this.faltas = Array.isArray(faltas.ausencias) ? faltas.ausencias : [];
        if (this.faltas.length === 0) {
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

        const today = new Date();
        const mes = today.getMonth() + 1; // Obtener mes actual
        const dia = today.getDate(); // Obtener día actual

        const response = await fetch(`http://127.0.0.1:8080/api/ausencias/mes/${mes}/dia/${dia}`, {
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
        return { ausencias: [] };
      }
    },
    renderTable() {
      const container = document.getElementById('faltas-container');
      const tableContainer = document.createElement('div');
      tableContainer.className = 'w-3/4';

      const table = document.createElement('table');
      table.className = 'min-w-full bg-white mt-4';

      const thead = document.createElement('thead');
      thead.className = 'bg-gray-800 text-white';
      thead.innerHTML = `
        <tr>
          <th class="w-1/6 px-4 py-2">Nombre</th>
          <th class="w-1/6 px-4 py-2">Fecha</th>
          <th class="w-1/6 px-4 py-2">Hora</th>
          <th class="w-1/6 px-4 py-2">Id ausencia</th>
          <th class="w-1/6 px-4 py-2">Aula</th>
          <th class="w-1/6 px-4 py-2">Grupo</th>
        </tr>
      `;
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      this.faltas.forEach(falta => {
        const horariosDelDia = falta.user.horarios.filter(horario => horario.dia === this.diaSemana);
        console.log('Horarios del día:', horariosDelDia);
        if (horariosDelDia.length > 0) {
          horariosDelDia.forEach(horario => {
            const tr = document.createElement('tr');
            tr.innerHTML = this.getReadOnlyRow(falta, horario);
            tbody.appendChild(tr);
          });
        } else {
          const tr = document.createElement('tr');
          tr.innerHTML = this.getNoDataRow(falta);
          tbody.appendChild(tr);
        }
      });
      table.appendChild(tbody);

      tableContainer.appendChild(table);
      container.innerHTML = '';
      container.appendChild(tableContainer);
    },
    getReadOnlyRow(falta, horario) {
      const fechaFormateada = new Date(falta.fecha).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });

      const hora = falta.hora ? falta.hora : 'todo el día';

      return `
        <td class="border px-4 py-2 text-black">${falta.user.name}</td>
        <td class="border px-4 py-2 text-black">${fechaFormateada}</td>
        <td class="border px-4 py-2 text-black">${hora}</td>
        <td class="border px-4 py-2 text-black">${falta.id}</td>
        <td class="border px-4 py-2 text-black">${horario.aula.descripcion}</td>
        <td class="border px-4 py-2 text-black">${horario.grupo.descripcion}</td>
      `;
    },
    getNoDataRow(falta) {
      const fechaFormateada = new Date(falta.fecha).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });

      const hora = falta.hora ? falta.hora : 'todo el día';

      return `
        <td class="border px-4 py-2 text-black">${falta.user.name}</td>
        <td class="border px-4 py-2 text-black">${fechaFormateada}</td>
        <td class="border px-4 py-2 text-black">${hora}</td>
        <td class="border px-4 py-2 text-black">${falta.id}</td>
        <td class="border px-4 py-2 text-black" colspan="2">No hay datos</td>
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
.min-w-full {
  min-width: 100%;
}
</style>
