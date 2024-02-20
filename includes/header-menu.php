<?php

add_shortcode('gp_header_primary_menu', 'gp_header_primary_menu');
function gp_header_primary_menu()
{

  ob_start();
?>
  <nav id="mainnav" class="mainnav">
    <ul class="menu">
      <li class="home">
        <a href="/tipos-de-impresion/">Tipos de Impresión</a>
        <ul class="submenu">
          <li>
            <a href="/eco/">
              <img src="/wp-content/uploads/2022/04/Sustainable-printing.jpg" class="li-menu-img">
              <p>SOSTENIBLE</p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/impresion-digital-textil-o-dtg/">
              <img src="/wp-content/uploads/2021/05/dtg-direct-to-garment-printing.jpeg" class="li-menu-img">
              <p>DIGITAL </p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/directo-a-film/">
              <img src="/wp-content/uploads/2022/04/DTF.jpg" class="li-menu-img">
              <p>DTF</p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/serigrafia/">
              <img src="/wp-content/uploads/2021/05/screen-printing.jpeg" class="li-menu-img">
              <p>Serigrafía</p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/bordados-personalizados/">
              <img src="/wp-content/uploads/2022/04/embroidery.png" class="li-menu-img">
              <p>BORDADOS </p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/transfer-textil/">
              <img src="/wp-content/uploads/2021/05/transfer-printing.jpeg" class="li-menu-img">
              <p>TRANSFER </p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/sublimacion-textil-o-full-print/">
              <img src="/wp-content/uploads/2021/05/sublimation-printing.jpeg" class="li-menu-img">
              <p>SUBLIMACIÓN</p>
            </a>
          </li>
          <li>
            <a href="/tipos-de-impresion/vinilo-de-corte/">
              <img src="/wp-content/uploads/2021/05/cad-printing.jpeg" class="li-menu-img">
              <p>VINILO </p>
            </a>
          </li>
          <li>
            <a href="/hecho-a-medida/">
              <img src="/wp-content/uploads/2021/05/thread-cut.svg" class="li-menu-img li-menu-svg">
              <p>Hecho a medida</p>
            </a>
          </li>
        </ul><!-- /submenu -->
      </li>

		
      <li>
        <a href="#">Recursos</a>
        <ul class="submenu next-submenu">
          <li>
            <a href="/guia-de-lavado-y-cuidado-de-prendas/">
              <img src="/wp-content/uploads/2022/04/washing-guidlines.jpeg" class="li-menu-img">
              <p>CUIDADOS DE LAVADO</p>
            </a>
          </li>
          <li>
            <a href="/pautas-y-tamanos-de-impresion/">
              <img src="/wp-content/uploads/2022/04/print-guidelines.jpeg" class="li-menu-img">
              <p>Pautas y Tamaños</p>
            </a>
          </li>
          <li>
            <a href="/proyectos-realizados/">
              <img src="/wp-content/uploads/2022/04/Projects-2.jpg" class="li-menu-img">
              <p>Proyectos realizados</p>
            </a>
          </li>
          <li>
            <a href="/blog/">
              <img src="/wp-content/uploads/2022/04/blog.png" class="li-menu-img">
              <p>BLOG</p>
            </a>
          </li>
        </ul><!-- /.submenu -->
      </li>
    </ul><!-- /.menu -->
  </nav>
<?php
  $data = ob_get_contents();
  ob_get_clean();
  return $data;
}
// =====================================================


add_shortcode('gp_all_product_menu', 'gp_all_product_menu');
function gp_all_product_menu()
{

  ob_start();
?>
  <div class='navbar'>
    <div class='dropdown'>
      <div class='dropbtn'>Todos los Productos
      </div>
      <div class='dropdown-content'>

        <div class='row'>

          <div class='column'>
            <h5 class="column-heading">Ropa Personalizada</h5>
            <a href='/banadores'>Bañadores</a>
            <a href='/batas"'>Batas</a>
            <a href='/bermudas/'>Bermudas</a>
            <a href='/bolsas/'>Bolsas</a>
            <a href='/calcetines-personalizados/'>Calcetines</a>
            <a href='/calzado'>Calzado</a>
            <a href='/camisas-personalizadas'>Camisas</a>
            <a href='/camisetas-personalizadas/'>Camisetas </a>
            <a href='/chalecos/'>Chalecos</a>
            <a href='/chanclas/'>Chanclas</a>
            <a href='/chaquetas/'>Chaquetas</a>
            <a href='/gorras-personalizadas/'>Gorras</a>
            <a href='/gorros'>Gorros</a>
            <a href='/guantes/'>Guantes y Bufandas</a>
            <a href='/leggings'>Leggings</a>
            <a href='/mascarillas-personalizadas/'>Mascarillas </a>
            <a href='/monos/'>Monos</a>
          </div>

          <div class='column'>
            <h5> </h5>
            <a href='/pantalones/'>Pantalones</a>
            <a href='/polos-bordados-personalizados/'>Polos</a>
            <a href='/rinoneras'>Riñoneras</a>
            <a href='/ropa-interior/'> Ropa Interior</a>
            <a href='/gorras-personalizadas/sombreros/'> Sombreros</a>
            <a href='/sudaderas-con-capucha/'>Sudaderas con Capucha </a>
            <a href='/sudaderas-personalizadas/'>Sudaderas</a>
            <a href='/toallas-y-hogar/'>Toallas y Hogar </a>
            <a href='/vestidos/'>Vestidos</a>
			  
			<h5 class="column-heading">Colecciones</h5>
            <a href='/ropa-corporativa/'>Corporativa</a>
            <a href='/ropa-deportiva/'>Deportiva</a>
            <a href='/eco' class="text-green">ECO</a>
            <a href='/horeca/'>Horeca</a>
            <a href='/ropa-ninos'>Niños</a>
            <a href='/mayor-visibilidad/'>Mayor Visibilidad</a>
            <a href='/categoria-profesional/'>Profesional </a>
          </div>

          <div class='column'>
            <h5> </h5>
            <a href='/polares/'>Polares</a>
            <a href='/softshell'>Softshell</a>
            <a href='/ropa-nautica/'>Ropa Náutica</a>
           

            <h5 class="column-heading">Productos Publicitarios</h5>
            <a href='/productos-de-merchandising/'>Altavoces</a>
            <a href='/productos-de-merchandising/'>Bidones</a>
            <a href='/productos-de-merchandising/'>Bolis</a>
            <a href='/productos-de-merchandising/' class='text-green'>ECO</a>
			<a href='/productos-de-merchandising/'>Gorras</a>
			<a href='/productos-de-merchandising/'>Lanyards</a>
			<a href='/productos-de-merchandising/'>Memorias USB</a>
			<a href='/productos-de-merchandising/'>Mugs</a>
			<a href='/productos-de-merchandising/'>Powerbanks</a>
			<a href='/productos-de-merchandising/'>Selfie Sticks</a>         
          </div>

          <div class='column last-column'>
			  <div class="contacto_wrap">
            	<h5>¿No encuentras <br> lo que buscas?</h5>
            	<a href='/contacto/' class='gp_button'>CONTACTAR</a>
			 </div>
          </div>

        </div>
      </div>
    </div>
  </div>
<?php
  $data = ob_get_contents();
  ob_get_clean();
  return $data;
}