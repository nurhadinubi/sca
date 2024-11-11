<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>
                <li>
                    <a href="/">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                {{-- Approval Pending --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-text"></i>
                        <span data-key="t-apps">Approve Dokumen
                            <span class="badge text-bg-secondary"> {{ $scaleupPending + $keycodePending }} </span>
                        </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            {{-- @dd($scaleupPending) --}}
                            <a href="{{ route('list.approval.scaleup') }}">
                                <span data-key="t-calendar">Approve Scale Up
                                    @if (isset($scaleupPending) && $scaleupPending > 0)
                                        <span class="badge text-bg-secondary">{{ $scaleupPending }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li>

                            <a href="{{ route('list.approval.keycode') }}">
                                <span data-key="t-calendar">Approve Key Code 
                                    @if (isset($keycodePending) && $keycodePending > 0)
                                        <span class="badge text-bg-secondary">{{ $keycodePending }}</span>
                                    @endif
                                  
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>


                <hr />
                {{-- Keycode --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="key"></i>
                        <span data-key="t-apps">Keycode</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @if (!auth()->user()->hasanyrole('admin') && auth()->user()->can('scaleup-create'))
                            <li>
                                <a href="{{ route('keycode.input') }}">
                                    <span data-key="t-keycode-input">Proses Keycode</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('keycode.menu') }}">
                                    <span data-key="t-keycode-create">Request Key Code</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('keycode.pending') }}">
                                <span data-key="t-calendar">List Pending Keycode</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('keycode.complete') }}">
                                <span data-key="t-calendar">List Completed Keycode</span>
                            </a>
                        </li>

                    </ul>
                </li>
                {{-- Scale Up --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="slack"></i>
                        <span data-key="t-apps">Scale Up</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @if (!auth()->user()->hasanyrole('admin|approver') && auth()->user()->can('scaleup-create'))
                            <li>
                                <a href="{{ route('keycode.input') }}">
                                    <span data-key="t-scaleup-create">Create Scale Up</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('scaleup.index') }}">
                                <span data-key="t-calendar">List Scale Up Aktif</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scaleup.complete') }}">
                                <span data-key="t-calendar">List Scale Up Completed</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scaleup.submit') }}">
                                <span data-key="t-calendar">Submit Scaleup</span>
                            </a>
                        </li>


                        {{-- @can('scaleup-compare')
                            <li>
                                <a href="{{ route('scaleup.compare') }}">
                                    <span data-key="t-chat">Compare Scale Up</span>
                                </a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
                {{-- Draft --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="folder"></i>
                        <span data-key="t-apps">Draft</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">

                        <li>
                            <a href="{{ route('draft.scaleup.index') }}">
                                <span data-key="t-calendar">Scale Up</span>
                            </a>
                        </li>


                        {{-- @can('scaleup-compare')

                        <li>
                            <a href="{{ route('scaleup.compare') }}">
                                <span data-key="t-chat">Compare Scale Up</span>
                            </a>
                        </li>
                        @endcan --}}
                    </ul>
                </li>
                {{-- Formual --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-plus"></i>
                        <span data-key="t-formula">Formula</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('formula-list')
                            <li>
                                <a href="{{ route('sf.index') }}">
                                    <span data-key="t-formula">List Formula</span>
                                </a>
                            </li>
                        @endcan

                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-level-1-2">Tambah Formula</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" data-key="t-sf">Formula SFG</a></li>
                                <li><a href="javascript: void(0);" data-key="t-fg">Formula Packing</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-level-1-2">Formula
                                Alternatif</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" data-key="t-sf">Alternatif SFG</a></li>
                                <li><a href="javascript: void(0);" data-key="t-fg">Alternatif Packing</a></li>
                            </ul>
                        </li>

                        @can('formula-compare')
                            <li>
                                <a href="#">
                                    <span data-key="t-formula-compare">Compare Formula</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                <hr />

                {{-- Material --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="archive"></i>
                        <span data-key="t-materials">Material</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('ci.list') }}">
                                <span data-key="t-list-material">List Produk CI</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ci.rm') }}">
                                <span data-key="t-list-rm">List Produk RM</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ci.create') }}">
                                <span data-key="t-material-ci">Tambah Produk CI</span>
                            </a>
                        </li>

                        {{-- <li>
                            <a href="{{ route('pc.create') }}">
                                <span data-key="t-product-code">Map Product - SAP</span>
                            </a>
                        </li> --}}
                        <li>
                            <a href="{{ route('sap.createRM') }}">
                                <span data-key="t-rm-code">Create Material RM</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @role('admin')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="user"></i>
                            <span data-key="t-users">User</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('user.index') }}">
                                    <span data-key="t-list-user">List User</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('user.create') }}">
                                    <span data-key="t-iuser-create">Tambah User</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('division.index') }}">
                                    <span data-key="t-iuser-dept">Divisi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sub-div.index') }}">
                                    <span data-key="t-sub-div">Sub Divisi</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('permission.addRoleToUser') }}">
                                    <span data-key="t-user-role">Assign Role</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('permission.addPermissionToUser') }}">
                                    <span data-key="t-user-permission">Assign Permission</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="file-plus"></i>
                            <span data-key="t-formula">Setting Approval</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('master.approval.index') }}">
                                    <span data-key="t-formula">List Approval</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('master.approval.create') }}">
                                    <span data-key="t-formula-create">Assign Approval</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="file-plus"></i>
                            <span data-key="t-formula">Master Data</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            


                        </ul>
                    </li> --}}
                @endrole
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
