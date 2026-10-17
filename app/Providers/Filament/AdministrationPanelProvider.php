<?php

namespace App\Providers\Filament;

use App\Filament\AvatarInitiales;
use App\Filament\Pages\Auth\Connexion;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Administration ALYA (/administration) : dirigeante et administrateurs.
 * Garde « personnel », double authentification par application obligatoire
 * pour tout compte (audit 4.8), même palette et mêmes polices que la vitrine.
 */
class AdministrationPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Dates à la française et montants en FCFA dans tous les tableaux et fiches.
        foreach ([Table::class, Schema::class] as $composant) {
            $composant::configureUsing(fn (Table|Schema $instance) => $instance
                ->defaultDateDisplayFormat('d/m/Y')
                ->defaultDateTimeDisplayFormat('d/m/Y H:i')
                ->defaultCurrency('xof')
                ->defaultNumberLocale('fr'));
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('administration')
            ->path('administration')
            ->authGuard('personnel')
            ->authPasswordBroker('utilisateurs')
            ->login(Connexion::class)
            ->passwordReset()
            ->profile(isSimple: false)
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->brandName('ALYA Cosmetic')
                    ->recoverable(),
                isRequired: true,
            )
            ->brandName('ALYA')
            ->defaultAvatarProvider(AvatarInitiales::class)
            ->darkMode(true, isForced: true)
            // Palette de l'audit 9.3. Les gris sont les noirs ALYA (950 = noir
            // profond, 900 = surface, 800 = relief, 700 = filet) remontant vers l'ivoire.
            ->colors([
                'primary' => [
                    // En sombre, Filament peint ses boutons en 600 (survol 500) et ses liens en 400 :
                    // ces trois teintes restent sur l'or champagne, le survol tire vers l'or clair.
                    50 => '#f8f3e6', 100 => '#f1e7cc', 200 => '#e9d9ae', 300 => '#dcc386',
                    400 => '#c8a95a', 500 => '#d6bd78', 600 => '#c8a95a', 700 => '#7d6432',
                    800 => '#5c4a26', 900 => '#3d321b', 950 => '#241e11',
                ],
                'gray' => [
                    50 => '#f3ede2', 100 => '#ebe5d9', 200 => '#d6d0c4', 300 => '#b3ada1',
                    400 => '#8e877b', 500 => '#6c665b', 600 => '#4a453c', 700 => '#2a2620',
                    800 => '#1a1a1a', 900 => '#111111', 950 => '#070707',
                ],
                'success' => Color::hex('#3e7b5c'),
                'warning' => Color::hex('#b8862b'),
                'danger' => Color::hex('#a03a32'),
                'info' => Color::hex('#8e877b'),
            ])
            ->font('Jost', provider: LocalFontProvider::class)
            ->viteTheme('resources/css/filament/administration/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
