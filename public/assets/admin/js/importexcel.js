const dropZone = document.getElementById('dropZone');
     const fileInput = document.getElementById('fileInput');
     const fileDragged = document.getElementById('fileDragged');

     // Al hacer clic en la zona de arrastre, abre el input de archivo
     dropZone.addEventListener('click', () => fileInput.click());

     // Cambia el texto en la zona de arrastre cuando se selecciona un archivo
     fileInput.addEventListener('change', (event) => {
         if (event.target.files.length > 0) {
             dropZone.textContent = `Archivo seleccionado: ${event.target.files[0].name}`;
         }
     });

     // Previene el comportamiento por defecto de arrastrar
     dropZone.addEventListener('dragover', (event) => {
         event.preventDefault();
         dropZone.classList.add('bg-light');
     });

     // Quita el fondo cuando el archivo no se está arrastrando
     dropZone.addEventListener('dragleave', () => {
         dropZone.classList.remove('bg-light');
     });

     // Maneja el archivo soltado
     dropZone.addEventListener('drop', (event) => {
         event.preventDefault();
         dropZone.classList.remove('bg-light');

         // Asignar el archivo soltado al input de archivo
         const files = event.dataTransfer.files;
         if (files.length > 0) {
             fileInput.files = files;
             dropZone.textContent = `Archivo seleccionado: ${files[0].name}`;
         }
     });