<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.English
 */

$spanish = array(
        /*profile */
        'coffee:profile:add:hobbiesandinterest' => 'Añadir un hobby o interés'
        , 'coffee:profile:hobbiesandinterest' => 'Hobby o interés'
        , 'coffee:profile:information:mobilephone' => 'celular'
        , 'coffee:profile:information:workphone' => 'oficina'
        , 'coffee:profile:information' => 'Información de contacto'
        , 'coffee:profile:presentation' => 'Presentación'
        , 'coffee:profile:button:background' => 'Fondo de pantalla'
        , 'coffee:profile:title:changecoverpic' => 'Cambiar tu fondo de pantalla'
        , 'coffee:profile:button:changeavatar' => 'Cambiar tu foto'
        , 'coffee:profile:add:presentation' => 'Añadir tu presentación'
        , 'coffee:profile:add:workphone' => 'Añadir tu telefono de oficina'
        , 'coffee:profile:add:mobilephone' => 'Añadir tu telefono de celular'
        , 'coffee:profile:addheadline' => 'Añadir tu título'
        , 'coffee:profile:addlocation' => 'Añadir tu ubicación'
        , 'coffee:poke:action' => '¡Coffee Poke!'
        , 'coffee:poke:body' => 'Tomemos un café'
        , 'coffee:poke:subject' => '¿Café?'
        , 'coffee:profile:incomplete' => '<strong>Este es tu perfil.</strong><br />¡Será visible para tus colegas, así que asegúrate de completarlo y mantenerlo actualizado!'
        /*feed*/
        , 'coffee:feed:share' => 'Comparte algo con tus colegas...'
        , 'coffee:feed:upload' => 'Subir algo...'
        , 'coffee:feed:send' => 'Enviar'
        , 'coffee:feeditem:showalltext' => 'Mostrar todo el texto'
        , 'coffee:feeditem:hidetext' => 'Esconder texto'
        , 'coffee:feeditem:likesthis' => 'les gusta esto'
        , 'coffee:feeditem:and' => 'y'
        , 'coffee:feeditem:others' => 'otros'
        , 'coffee:feeditem:showall' => 'Mostrar todo'
        , 'coffee:feeditem:comments' => 'comentarios'
        , 'coffee:feeditem:action:removecomment' => 'Borrar comentario'
        , 'coffee:feeditem:action:addcomment' => 'Escribir un comentario...'
        , 'coffee:feeditem:action:like' => 'Me gusta'
        , 'coffee:feeditem:action:unlike' => 'No me gusta'
        , 'coffee:feeditem:action:removecomment' => 'Borrar comentario'
        , 'coffee:feeditem:action:addcomment' => 'Escribir un comentario...'
        , 'coffee:feeditem:action:addcomment' => 'Escribir un comentario...'
        /*menu*/
        , 'coffee:menu:welcome' => 'Bienvenido'
        , 'coffee:menu:feedlist' => 'Muro'
        , 'coffee:menu:profile' => 'Perfil'
        , 'coffee:menu:tvapp' => 'Encender Coffee TV'
        , 'coffee:menu:logout' => 'Salir'
        , 'coffee:menu:admin' => 'Administración'
        , 'coffee:menu:settings' => 'Ajustes'
        /*welcome*/
        , 'coffee:welcome:headline' => '<h3>¡Bienvenido!</h3>
                        <p>En el mundo profesional, las buenas ideas llegan muchas veces por casualidad, durante discusiones informales, en un pasillo o alrededor de la maquina de café.</p>
                        <p>Por esta razón hemos creado CoffeePoke; una red social para empresas innovadora y amigable que permite intercambiar fácilmente con todos tus colegas.</p>
                        <p>Donde quiera estés:</p>
                        <ul>
                            <li>Te mantiene informado sobre la actualidad de tu empresa.</li>
                            <li>Comparte y comenta información, reflexiones y buenas ideas.</li>
                            <li>Invita a tus colegas a compartir un verdadero café gracias a la herramienta Coffee Poke.</li>
                        </ul>
                        '
        , 'coffee:welcome:instructions' => '
                            <h3>Pocos pasos para empezar...</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#profile">Personaliza tu perfil</a>
                                    <br />
                                    Comparte tus hobbies, añade una foto y elige un fondo de pantalla que te caracterice.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Publicar un mensaje</a>
                                    <br />
                                    Comenta en lo estas trabajando, comparte información, reflexiones, ideas, artículos o imagen, pide consejos…
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">¡Sé activo!</a>
                                    <br />
                                    Interactúa con el contenido publicado y enriquécelo con tu punto de vista y experiencias personales. Manténte conectado para desarrollar el espíritu de compartir.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Accesibilidad y movilidad</a>
                                    <br />
                                    CoffeePoke está disponible en todas partes y todo el tiempo; en tu computadora, tu smartphone, y en los lugares públicos de tu empresa gracias a la aplicación TV.
                                    <br />
                                </span>
                            </div>

                            <p><h3><a href="/static/doc/CoffeePoke_es.pdf" target="_blank">Más info</a> para obtener el mejor de CoffeePoke.</h3></p>'
                    //DATES
                    ,'friendlytime:justnow' => "ahora"
                    ,'friendlytime:minutes' => "hace %s minutos"
                    ,'friendlytime:minutes:singular' => "hace un minuto"
                    ,'friendlytime:hours' => "hace %s horas"
                    ,'friendlytime:hours:singular' => "hace una hora"
                    ,'friendlytime:days' => "hace %s días"
                    ,'friendlytime:days:singular' => "ayer"
                    ,'friendlytime:date_format' => 'j F Y à H:i'
/**
 * User add
 */

	,'useradd:subject' => 'User account created'
	,'useradd:body' => '
%1$s,

A user account has been created for you at %2$s. To log in, visit:

http://%2$s.coffeepoke.com/

And log in with these user credentials:

Username: %4$s
Password: %5$s

Enjoy coffeePoke!!',

);

add_translation("es",$spanish);
