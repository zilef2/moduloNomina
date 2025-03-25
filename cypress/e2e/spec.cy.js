describe('Prueba de formulario con valores aleatorios', () => {
  it('Debe rellenar el formulario con datos aleatorios y enviarlo', () => {
    // Visitar la página donde está el formulario
    cy.visit('/ruta-a-tu-formulario'); // Reemplaza con la ruta correcta

    // Generar un nombre aleatorio
    const nombreAleatorio = `Usuario${Math.floor(Math.random() * 1000)}`;
    cy.get('#nombre').type(nombreAleatorio); // Reemplaza '#nombre' si es diferente

    // Generar un correo electrónico aleatorio
    const emailAleatorio = `test${Math.floor(Math.random() * 1000)}@example.com`;
    cy.get('#email').type(emailAleatorio); // Reemplaza '#email' si es diferente

    // Generar un número aleatorio
    const numeroAleatorio = Math.floor(Math.random() * 100);
    cy.get('#edad').type(numeroAleatorio.toString()); // Reemplaza '#edad' si es diferente

    // Seleccionar una opción aleatoria de un desplegable (si existe)
    cy.get('#seleccion').select(Math.floor(Math.random() * 3) + 1); // Asume 3 opciones, ajusta si es necesario

    // Marcar un checkbox aleatoriamente (si existe)
    cy.get('#checkbox').check(); // Reemplaza '#checkbox' si es diferente

    // Hacer clic en un botón de radio aleatoriamente (si existen)
    cy.get('input[type="radio"]').then($radioButtons => {
      const randomIndex = Math.floor(Math.random() * $radioButtons.length);
      cy.wrap($radioButtons[randomIndex]).check();
    });

    // Hacer clic en el botón de enviar
    cy.get('#enviar').click(); // Reemplaza '#enviar' con el ID o selector correcto de tu botón de envío

    // Puedes agregar aserciones aquí para verificar que el formulario se envió correctamente
    // Por ejemplo, verificar un mensaje de éxito o una redirección
    cy.contains('Formulario enviado con éxito');
  });
});