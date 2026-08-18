            </div>
        <footer class="app-footer">
            <span>{{ setting('app_name', config('app.name', 'Laravel')) }} &copy; {{ date('Y') }}</span>
            <span>Hecho ❤️ por MambaCode</span> 
        </footer>
    </main>
</div>
@include('partials.alerts')
@stack('scripts')
</body>
</html>
