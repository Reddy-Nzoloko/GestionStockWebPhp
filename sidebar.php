<div class="lg:hidden fixed top-4 left-4 z-50">
    <button id="mobile-open" class="bg-gray-900 text-white p-2 rounded-lg shadow-lg">
        <i class="fas fa-bars fa-lg"></i>
    </button>
</div>

<div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

<div id="sidebar" class="fixed lg:static inset-y-0 left-0 w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col h-screen shadow-xl z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <div class="p-6 text-2xl font-black tracking-wider border-b border-gray-800 text-blue-400 flex justify-between items-center">
        <span><i class="fas fa-warehouse mr-2"></i> STOCK<span class="text-white">PRO</span></span>
        <button id="mobile-close" class="lg:hidden text-gray-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 mt-4 overflow-y-auto custom-scrollbar">
        <a href="index.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-chart-line mr-3 w-5 text-center"></i> Dashboard
        </a>

        <div class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Gestion</div>
        <a href="produits.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'produits.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-box mr-3 w-5 text-center"></i> Produits
        </a>
        <a href="categories.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-tags mr-3 w-5 text-center"></i> Catégories
        </a>
        <a href="mouvements.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'mouvements.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-exchange-alt mr-3 w-5 text-center"></i> Mouvements
        </a>

        <div class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Partenaires</div>
        <a href="clients.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-users mr-3 w-5 text-center"></i> Clients
        </a>
        <a href="fournisseurs.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'fournisseurs.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-truck-loading mr-3 w-5 text-center"></i> Fournisseurs
        </a>

        <div class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Analyse</div>
        <a href="bilan.php" class="flex items-center py-3 px-6 hover:bg-gray-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'bilan.php' ? 'bg-blue-600 border-l-4 border-white' : ''; ?>">
            <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center text-yellow-400"></i> Bilan Financier
        </a>
    </nav>

    <div class="border-t border-gray-800 bg-gray-950 p-4">
        <div class="flex justify-around mb-4">
            <a href="https://github.com/RedDev" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-github fa-lg"></i></a>
            <a href="https://instagram.com/RedDev" target="_blank" class="text-pink-500 hover:text-pink-400"><i class="fab fa-instagram fa-lg"></i></a>
            <a href="https://wa.me/243000000000" target="_blank" class="text-green-500 hover:text-green-400"><i class="fab fa-whatsapp fa-lg"></i></a>
        </div>

        <a href="logout.php" class="flex items-center py-2 px-4 text-red-400 hover:bg-red-500 hover:text-white rounded transition mb-4">
            <i class="fas fa-sign-out-alt mr-3"></i> Déconnexion
        </a>

        <div class="text-center">
            <p class="text-[10px] text-gray-500 uppercase font-bold">
                &copy; <?php echo date('Y'); ?> | <span class="text-blue-400 italic">Developed by RedDev</span>
            </p>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const btnOpen = document.getElementById('mobile-open');
    const btnClose = document.getElementById('mobile-close');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    btnOpen.addEventListener('click', toggleSidebar);
    btnClose.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
</script>

<style>
    /* Scrollbar personnalisée pour le menu nav */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
</style>