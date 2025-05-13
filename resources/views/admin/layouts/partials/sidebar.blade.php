<nav class="sidebar sidebar-offcanvas border border-right-dark shadow-sm" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="{{ route('usuario.perfil') }}" class="nav-link">
                <div class="nav-profile-image">
                    @if (Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" width="100">
                    @else
                        <img src="{{ asset('assets/admin/images/faces/face1.jpg') }}" alt="profile" width="100">
                    @endif
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    @auth
                        <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                        <span class="text-secondary text-small">{{ Auth::user()->email }}</span>
                    @else
                        <span class="font-weight-bold mb-2">Guest</span>
                        <span class="text-secondary text-small">Please Login</span>
                    @endauth
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        @can('cambiar sucursales')
            <li class="nav-item">
                <form action="{{ route('updateSucursal') }}" method="POST" id="sucursalForm">
                    @csrf
                    <label for="sucursal_id">Selecciona una Sucursal:</label>
                    <select name="sucursal_id" id="sucursal_id" class="form-control">
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" @if ($sucursal->id == auth()->user()->branch_id) selected @endif>
                                {{ $sucursal->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </li>
        @endcan

        <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <span class="menu-title"><strong>Tablero</strong></span>
                <i class="mdi mdi-view-dashboard menu-icon"></i>
            </a>
        </li>
<li class="nav-item {{ request()->routeIs('categories.*', 'brands.*', 'products.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-articulos"
                aria-expanded="{{ request()->routeIs('categories.*', 'brands.*', 'products.*') ? 'true' : 'false' }}"
                aria-controls="ui-articulos">
                <span class="menu-title"><strong>Artículos</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-table-large menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('categories.*', 'brands.*', 'products.*') ? 'show' : '' }}"
                id="ui-articulos">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">
                            Categorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('brands.index') ? 'active' : '' }}"
                            href="{{ route('brands.index') }}">
                            Marcas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">
                            Productos
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item {{ request()->routeIs('cashregisters.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('cashregisters.index') }}">
                <span class="menu-title">Caja</span>
                <i class="mdi mdi-credit-card-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('purchase.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-compras"
                aria-expanded="{{ request()->routeIs('purchase.*') ? 'true' : 'false' }}" aria-controls="ui-compras">
                <span class="menu-title"><strong>Compras</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-shopping menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('purchase.*') ? 'show' : '' }}" id="ui-compras">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('purchase.index') ? 'active' : '' }}"
                            href="{{ route('purchase.index') }}">
                            Nueva Compra
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('purchase.details.index') ? 'active' : '' }}"
                            href="{{ route('purchase.details.index') }}">
                            Historial Compras
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        
        <li class="nav-item {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('suppliers.index') }}">
                <span class="menu-title">Proveedores</span>
                <i class="mdi mdi-truck menu-icon"></i>
            </a>
        </li>

        
        


        <li class="nav-item {{ request()->routeIs('sales.*', 'creditsales.index') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-ventas"
                aria-expanded="{{ request()->routeIs('sales.*', 'creditsales.index') ? 'true' : 'false' }}"
                aria-controls="ui-ventas">
                <span class="menu-title"><strong>Ventas</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-cash-multiple menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('sales.*', 'creditsales.index') ? 'show' : '' }}"
                id="ui-ventas">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}"
                            href="{{ route('sales.index') }}">
                            Nueva Venta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sales.details.index') ? 'active' : '' }}"
                            href="{{ route('sales.details.index') }}">
                            Historial Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <!--<a class="nav-link {{ request()->routeIs('creditsales.index') ? 'active' : '' }}"
                            href="{{ route('creditsales.index') }}">
                            Administrar Créditos
                        </a>-->
                    </li>
                </ul>
            </div>
        </li>

        
        <li class="nav-item {{ request()->routeIs('customers.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('customers.index') }}">
                <span class="menu-title">Clientes</span>
                <i class="mdi mdi-account-switch menu-icon"></i>
            </a>
        </li>

        
        <li class="nav-item {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-cotizacion"
                aria-expanded="{{ request()->routeIs('quotes.*') ? 'true' : 'false' }}"
                aria-controls="ui-cotizacion">
                <span class="menu-title"><strong>Cotizaciones</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-file-pdf menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('quotes.*') ? 'show' : '' }}" id="ui-cotizacion">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('quotes.index') ? 'active' : '' }}"
                            href="{{ route('quotes.index') }}">
                            Nueva Cotización
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('quotes.details.index') ? 'active' : '' }}"
                            href="{{ route('quotes.details.index') }}">
                            Historial Cotizaciones
                        </a>
                    </li>
                </ul>
            </div>
        </li>

<li class="nav-item {{ request()->routeIs('repairs.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-reparacion"
                aria-expanded="{{ request()->routeIs('repairs.*') ? 'true' : 'false' }}" aria-controls="ui-reparacion">
                <span class="menu-title"><strong>Taller</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-cellphone-iphone menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('repairs.*') ? 'show' : '' }}" id="ui-reparacion">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('repairs.index') ? 'active' : '' }}"
                            href="{{ route('repairs.index') }}">
                            Nueva Orden
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('repairs.details.index') ? 'active' : '' }}"
                            href="{{ route('repairs.details.index') }}">
                            Historial Ordenes
                        </a>
                    </li>
                </ul>
            </div>
        </li>


        <li
            class="nav-item {{ request()->routeIs('branchs.*', 'roles.*', 'users.*', 'payment.methods.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ui-administracion"
                aria-expanded="{{ request()->routeIs('branchs.*', 'roles.*', 'users.*', 'payment.methods.*') ? 'true' : 'false' }}"
                aria-controls="ui-administracion">
                <span class="menu-title"><strong>Administración</strong></span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-settings menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('branchs.*', 'roles.*', 'users.*', 'payment.methods.*') ? 'show' : '' }}"
                id="ui-administracion">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('branchs.index') ? 'active' : '' }}"
                            href="{{ route('branchs.index') }}">
                            Sucursales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">
                            Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payment.methods.index') ? 'active' : '' }}"
                            href="{{ route('payment.methods.index') }}">
                            Forma Pagos
                        </a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</nav>
