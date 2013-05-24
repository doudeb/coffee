<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.spain
 * @versiondate 171212
 */

$translation = array(
/*menu*/
         'coffee:menu:welcome' => 'Bienvenid@'
        , 'coffee:menu:feedlist' => 'CoffeeWall'
        , 'coffee:menu:profile' => 'Perfil'
        , 'coffee:menu:tvapp' => 'Encender CoffeeTV'
        , 'coffee:menu:admin' => 'Administración'
        , 'coffee:menu:settings' => 'Ajustes'
        , 'coffee:menu:logout' => 'Salir'
        , 'coffee:menu:people' => 'Directorio'
        , 'coffee:menu:notifications' => 'Notificaciones'

/*welcome*/
        , 'coffee:welcome:headline' => '<h3>Hola!</h3>
                       <p>Bienvenid@ a CoffeePoke. Este mensaje aparecerá únicamente durante tus tres primeras conexiones para familiarizarte con la aplicación.</p>
                        <p><a href="/static/doc/CoffeePoke_es.pdf" target="_blank">Haz click</a> para entender como CoffeePoke te permite ser todavía más eficaz (descargando un documento pdf).</p>
                        <p>Que tengas buen día con CoffeePoke.</p>'
        , 'coffee:welcome:instructions' => '
                            <h3>Prueba Coffee break 2.0</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#feed">CoffeeWall</a>
                                    <br />
                                    Comparte y comenta mensajes públicos y documentos con tus colegas. Pon el signo # antes de las palabras clave para poder hacerles clic y facilitar el acceso a la información.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#profile">Perfil</a>
                                    <br />
                                    Descubre a tus colegas y conóscanse de una nueva manera. Comparte tus gustos, elige un fondo de pantalla que te represente, personaliza tu presentación y por supuesto añade tu foto de perfil.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#tv">CoffeeTV</a>
                                    <br />
                                    No tienes tiempo para consultar la aplicación? Disfruta del coffee break para mantenerte informado! Amigable y colorida, CoffeeTV facilita la difusión de mensajes e información gracias a sus pantallas situadas en los lugares de vida de tu empresa. No cuentan con pantallas en tu empresa? Háblale a tu Community Manager (administrador) y CoffeePoke se ocupa de todo!
                                    <br />
                                </span>
                            </div>'

/*feed*/
        , 'coffee:feed:share' => 'Comparte algo con tus colegas...'
        , 'coffee:feed:upload' => 'Subir algo...'
        , 'coffee:feed:send' => 'Enviar'
        , 'coffee:feed:cancel' => 'Cancelar'
        , 'coffee:feed:search' => 'Buscar'
        , 'coffee:feed:corporatetags' => 'Sugerencias de hashtags'
        , 'coffee:feed:mostused' => 'Más usados'
        , 'coffee:feeditem:showalltext' => 'Mostrar comentarios anteriores'
        , 'coffee:feeditem:hidetext' => 'Ocultar comentarios anteriores'
        , 'coffee:feeditem:likesthis' => 'le gusta esto'
        , 'coffee:feeditem:likethis' => 'les gusta esto'
        , 'coffee:feeditem:and' => 'y'
        , 'coffee:feeditem:others' => 'otros'
        , 'coffee:feeditem:showall' => 'Ver los'
        , 'coffee:feeditem:comments' => 'comentarios'
        , 'coffee:feeditem:action:removecomment' => 'Borrar comentario'
        , 'coffee:feeditem:action:addcomment' => 'Escribir un comentario...'
        , 'coffee:feeditem:action:like' => 'Me gusta'
        , 'coffee:feeditem:action:unlike' => 'No me gusta'
        , 'coffee:feeditem:action:removecomment' => 'Borrar comentario'
        , 'coffee:feeditem:action:openlinkconfirm' => 'Esta acción va a abrir una nueva página.'
		, 'coffee:feed:broadcastmessageunactive' => 'Activar Mensaje Oficial'
		, 'coffee:feed:broadcastmessage' => 'Desactivar Mensaje Oficial'
		, 'coffee:feed:search' => 'Búsqueda'
		, 'coffee:feed:corporatetags' => 'Sugerencias de hashtags'
		, 'coffee:feed:mostused' => 'Más usados'

/*profile */
        , 'coffee:profile:add:hobbiesandinterest' => 'Añadir un hobby o interés'
        , 'coffee:profile:hobbiesandinterest' => 'Hobby o interés'
        , 'coffee:profile:information:mobilephone' => 'celular'
        , 'coffee:profile:information:workphone' => 'oficina'
        , 'coffee:profile:information' => 'Información de contacto'
        , 'coffee:profile:presentation' => 'Presentación'
        , 'coffee:profile:button:background' => 'Fondo de pantalla'
        , 'coffee:profile:title:changecoverpic' => 'Cambiar tu fondo de pantalla'
        , 'coffee:profile:button:changeavatar' => 'Cambiar tu foto'
        , 'coffee:profile:add:presentation' => 'Añadir tu presentación'
        , 'coffee:profile:add:workphone' => 'Añadir tu teléfono de oficina'
        , 'coffee:profile:add:mobilephone' => 'Añadir tu teléfono de celular'
        , 'coffee:profile:addheadline' => 'Añadir tu título'
        , 'coffee:profile:addlocation' => 'Añadir tu ubicación'
        , 'coffee:poke:action' => '¡Coffee Poke!'
        , 'coffee:poke:body' => 'Tomemos un café'
        , 'coffee:poke:subject' => '¿Café?'
        , 'coffee:profile:incomplete' => '<strong>Este es tu perfil.</strong><br />Será visible para tus colegas, así que asegúrate de completarlo y mantenerlo actualizado!'

/*TVapp*/
		, 'coffee:tvapp:title' => 'Hola,'
		, 'coffee:tvapp:message' => 'Que quieres difundir en esta pantalla'
		, 'coffee:tvapp:button' => 'Responder'
		, 'coffee:tvapp:answer1' => 'Quiero ver los 10 últimos mensajes'
		, 'coffee:tvapp:fromusers' => 'de'
		, 'coffee:tvapp:fromusersall' => 'todos los usuarios'
		, 'coffee:tvapp:fromuserselect' => 'los usuarios siguientes:'
		, 'coffee:tvapp:fromuserselectusername' => 'Nombre'
		, 'coffee:tvapp:tagselect' => 'Únicamente los que contienen el/los hashtag(s) siguiente(s)'
		, 'coffee:tvapp:addhashtag' => 'hashtag'
		, 'coffee:tvapp:thanks' => 'Gracias !'
		, 'coffee:tvapp:cancel' => 'Cancelar'

/*admin*/
		, 'coffee:admin:message' => 'Esto es tu panel de administración CoffeePoke. <br/ > <a href="#userSettings">Accede a tus Ajustes.</a> '
		, 'coffee:admin:users' => 'Usuarios'
		, 'coffee:admin:search' => 'Búsqueda'
		, 'coffee:admin:addnewuser' => 'Añadir nuevo usuario'
		, 'coffee:admin:addnewusertitle' => 'Añadir nuevo usuario'
		, 'coffee:admin:displayname' => 'Nombre Apellido'
		, 'coffee:admin:email' => 'Email'
		, 'coffee:admin:password' => 'Elige una contraseña'
		, 'coffee:admin:confirmpassword' => 'Confirma la contraseña'
		, 'coffee:admin:admin' => 'Tacha abajo si quieres que tenga los derechos de administrador:'
		, 'coffee:admin:sendemail' => 'Tacha abajo si quieres que CoffeePoke le mande un email de bienvenida al nuevo usuario con su acceso de conexión:'
		, 'coffee:admin:language' => 'Idioma'
		, 'coffee:admin:languageES' => 'Español'
		, 'coffee:admin:languageFR' => 'Francés'
		, 'coffee:admin:languageEN' => 'Ingles'
		, 'coffee:admin:addnewusersave' => 'Crear el perfil'
		, 'coffee:admin:manageuser' => 'Gestionar los usuarios'
		, 'coffee:admin:popupdelete' => 'Estás segur@ de querer suprimir este usuario? <br /> Atención, esta operación es irreversible. Su perfil y todos sus mensajes serán suprimidos.'
		, 'coffee:admin:site' => 'Sitio'
		, 'coffee:admin:sitesettings' => 'Personalización'
		, 'coffee:admin:sitesettingstitle' => 'Personalización de tu plataforma'
		, 'coffee:admin:logo' => '<strong>Logo</strong><br/>Tamaño máximo recomendado: ancho: 300px - alto: 100px.'
		, 'coffee:admin:background' => '<strong>Fondo de pantalla</strong><br/>Tamaño minimo recomendado: 1920*1080 min. Peso máximo recomendado: 300px.'
		, 'coffee:admin:defaultlanguage' => 'Idioma por default'
		, 'coffee:admin:defaultlanguageEN' => 'Ingles'
		, 'coffee:admin:defaultlanguageES' => 'Español'
		, 'coffee:admin:defaultlanguageFR' => 'Francés'
		, 'coffee:admin:sitesettingssave' => 'Guardar'
		, 'coffee:admin:corporatehashtags' => 'Sugerencias de hashtags'
		, 'coffee:admin:corporatehashtagstitle' => 'Sugerencias de hashtags'
		, 'coffee:admin:corporatehashtagshelp' => 'Como administrador, tienes la posibilidad de sugerir algunos hashtags a tus usuarios. Los #hashtags son palabras clave que son cliqueables por el hecho de juntar el signo # al principio de la palabra. Estas sugerencias se encuentran en el CoffeeWall haciendo click en la flecha situada a la derecha del motor de búsqueda.'
		, 'coffee:admin:addhashtag' => 'hashtag'
		, 'coffee:admin:corporatehashtagssave' => 'Guardar'

/*usersettings*/
		, 'coffee:usersettings:message' => 'Bienvenid@ a tus ajustes.'
		, 'coffee:usersettings:usersettings' => 'Ajustes'
		, 'coffee:usersettings:name' => 'Nombre Apellido'
		, 'coffee:usersettings:currentpassword' => 'Contraseña actual'
		, 'coffee:usersettings:newpassword' => 'Elige una nueva contraseña'
		, 'coffee:usersettings:confirmnewpassword' => 'Confirma tu nueva contraseña'
		, 'coffee:usersettings:language' => 'Idioma'
		, 'coffee:usersettings:languageES' => 'Español'
		, 'coffee:usersettings:languageFR' => 'Francés'
		, 'coffee:usersettings:languageEN' => 'Ingles'
		, 'coffee:usersettings:save' => 'Guardar'

/*User add*/

		, 'useradd:subject' => '%1$s, bienvenid@ a CoffeePoke'
		, 'useradd:body' => '

Hola %1$s,

Te invito a unirte a nuestra comunidad %2$s en CoffeePoke.

CoffeePoke, nuestra red social corporativa, que permite intercambiar mensajes públicos en nuestro "CoffeeWall", de manera amigable.

Nuestro objetivo es fomentar los intercambios informales para conocernos más y favorecer la cohesión de grupo.

Te he creado una cuenta. Para acceder a ella haz click en : http://%2$s.coffeepoke.com/ y completa tus accesos:
Email : %4$s
Password : %5$s

No esperes para conectarte y pedir ayuda, compartir en lo que estas trabajando o actualizarte sobre %2$s.

Siempre en movimiento? No hay problema, conéctate desde el navegador de tu móvil.

Nos vemos en CoffeePoke!

%2$s

CoffeePoke, nuestro coffee break 2.0'

                    //DATES
                    ,'friendlytime:justnow' => "ahora"
                    ,'friendlytime:minutes' => "hace %s minutos"
                    ,'friendlytime:minutes:singular' => "hace un minuto"
                    ,'friendlytime:hours' => "hace %s horas"
                    ,'friendlytime:hours:singular' => "hace una hora"
                    ,'friendlytime:days' => "hace %s días"
                    ,'friendlytime:days:singular' => "ayer"
                    ,'friendlytime:date_format' => 'j F Y à H:i'

    , 'coffee:people:legend' => 'Directorio. Encuentra los perfiles de tus colaboradores.'
    , 'notification::like' => 'le gusta esto '
    , 'notification::comment' => 'ha comentado esta publicación '
    , 'notification::mention' => 'ha citado tu nombre en '


);

add_translation('es', $translation);