<div class="w-64 bg-gray-800 text-white flex-shrink-0">
    <div class="p-6 text-2xl font-black tracking-wider border-b border-gray-700 text-blue-400">
        <i class="fas fa-warehouse mr-2"></i> STOCK<span class="text-white">PRO</span>
    </div>
    <nav class="mt-6">
        <a href="index.php" class="flex items-center py-3 px-6 hover:bg-gray-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-600' : ''; ?>">
            <i class="fas fa-home mr-3 w-5 text-center"></i> Dashboard
        </a>
        <a href="produits.php" class="flex items-center py-3 px-6 hover:bg-gray-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'produits.php' ? 'bg-blue-600' : ''; ?>">
            <i class="fas fa-box mr-3 w-5 text-center"></i> Produits
        </a>
        <a href="categories.php" class="flex items-center py-3 px-6 hover:bg-gray-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'bg-blue-600' : ''; ?>">
            <i class="fas fa-tags mr-3 w-5 text-center"></i> Catégories
        </a>
        <a href="mouvements.php" class="flex items-center py-3 px-6 hover:bg-gray-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'mouvements.php' ? 'bg-blue-600' : ''; ?>">
            <i class="fas fa-exchange-alt mr-3 w-5 text-center"></i> Mouvements
        </a>
    </nav>
</div>