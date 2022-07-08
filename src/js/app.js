let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;



const cita ={
    nombre:'',
    fecha:'',
    hora:'',
    servicios:[]
}

document.addEventListener('DOMContentLoaded',function (){
    iniciarApp();
});

function iniciarApp(){
    mostrarSeccion(); //Muestra y ocula las secciones
    tabs(); //cambia las secciones cuando se presionen los tabs
    botonesPaginador(); //Agrega o quita los botones del paginador.
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); //Consulta la API en el backend de PHP

    nombreCliente(); //Añade el nombre del cliente al objeto de cita
    seleccionarFecha(); //Anade la fecha de la cita en el objeto
    seleccionarHora(); //Añade la hora de la cita en el objeto

    mostrarResumen();
}

function mostrarSeccion(){
    //Ocultar si tiene la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');

    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    //Seleccionar la seccion con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual')
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    //Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    //Iterando sobre los botones seleccionados para asignarles un evento
    botones.forEach(boton => {
        boton.addEventListener('click', (e) => {
            paso = parseInt(e.target.dataset.paso);

            //Cada que se cambie se pagina se llaman esta funciones para actualizar el FrontEnd
            mostrarSeccion();
            botonesPaginador();
        })
    })
}

function botonesPaginador(){
    const paginaSiguiente = document.querySelector('#siguiente');
    const paginaAnterior = document.querySelector('#anterior');

    if(paso === 1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar')
    }else if(paso === 3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click',function (){
        if(paso>=pasoFinal) return;
        paso++;
        botonesPaginador()
    })
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click',function (){
        if(paso<=pasoInicial){ return;} //Para que no se pase de la pagina inicial
        paso--;
        botonesPaginador()
    })
}

async function consultarAPI(){
    try{
        const url = 'http://localhost/api/servicios'
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    }catch (error){
        console.log('Error');
    }
}

function mostrarServicios(servicios){
    servicios.forEach( servicio =>{
        const {id,nombre,precio} = servicio;

        //Scripting
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id; //Atributo personalizado
        servicioDiv.onclick = function (){
            seleccionarServicio(servicio); //callback
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);

    });
}

function seleccionarServicio(servicio){
    const {id} = servicio;
    const {servicios} = cita;

    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`)


    //Identificar el elemento al que se le da click
    //Comprobar si un servicio ya fue agregado
    if(servicios.some(agregado => agregado.id === id)){
        //Eliminarlo
        cita.servicios = servicios.filter(agregado => agregado.id !== id);

        divServicio.classList.remove('seleccionado');
    }else{
        //agregarlo
        cita.servicios = [...servicios,servicio];
        divServicio.classList.add('seleccionado');
    }
}

const nombreCliente = () => cita.nombre = document.querySelector('#nombre').value;

const seleccionarFecha = () => {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function (e){
        const dia = new Date(e.target.value).getUTCDay();
        if([6,0].includes(dia)){
            e.target.value='';
            mostrarAlerta('Fines de semana no permitidos','error','.formulario');
        }else{
           cita.fecha = e.target.value;
        }
    })
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input',function (e){


        const horaCita = e.target.value;
        const hora = horaCita.split(':')[0];

        //Dentro del horario de atencion
        if(hora<10 || hora>18){
            e.target.value = '';
            mostrarAlerta('Horario de atencion: 10:00 - 18:00','error','.formulario');
        }else{
            cita.hora = e.target.value;
        }
    })
}

function mostrarAlerta(mensaje,tipo,elemento,desaparece = true){
    const existeAlerta = document.querySelector('.alerta');

    if(existeAlerta){
       existeAlerta.remove();
    }

    const alerta = document.createElement('div');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        setTimeout(()=>{
            alerta.remove();
        },3000)
    }
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido de resumen
    while (resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Faltan datos o no se ha seleccionado servicio','error','.contenido-resumen',false);
        return
    }

    //Formatear el div de resumen
    const {nombre,fecha,hora,servicios} = cita;



    const headerServicios = document.createElement('h3');
    headerServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headerServicios);

    servicios.forEach(servicio => {
        const {id,nombre,precio} = servicio;

        const contenedorServicio = document.createElement('div');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('p');
        textoServicio.textContent = nombre

        const precioServicio = document.createElement('p');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })

    //headin para cita en resumen
    const headerCita = document.createElement('h3');
    headerCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headerCita);

    const nombreCliente = document.createElement('p');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //Formartear la fecha en español
    const fechaOBJ =  new Date(fecha);
    const mes = fechaOBJ.getMonth();
    const dia = fechaOBJ.getDate() + 2;
    const year = fechaOBJ.getFullYear();

    const fechaUTC = new Date(Date.UTC(year,mes,dia));

    const opciones = {weekday: 'long',year:'numeric',month:'long', day:'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX',opciones);


    const fechaCita = document.createElement('p');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('p');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    //Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar)

}

async function reservarCita(){
    const datos = new FormData();
    datos.append('nombre','Juan');

    //Peticion hacia la api
    const url = 'http://localhost/api/citas';
    const respuesta = await fetch(url,{
        method: 'POST'
    });

    const resultado = await respuesta.json();
    console.log(resultado)

    /*console.log([...datos])*/
}

