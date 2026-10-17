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
 * Administration AMANI (/administration) : propriétaire et administrateurs.
 * Garde « personnel ». Le cahier (§12.3) impose la double authentification à
 * l'administrateur principal ; elle est ici obligatoire pour tout compte.
 * Même palette et mêmes polices que la vitrine.
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
                    ->brandName('AMANI Cosmetics')
                    ->recoverable(),
                isRequired: true,
            )
            ->brandName('AMANI Cosmetics')
            ->brandLogo(fn () => view('filament.marque'))
            ->brandLogoHeight('2.6rem')
            ->defaultAvatarProvider(AvatarInitiales::class)
            ->darkMode(false)
            // Palette du cahier AMANI et des maquettes. En thème clair, Filament
            // peint ses boutons en 600 (survol 500) et ses liens en 600, à condition
            // que le texte blanc y reste lisible (contraste 4,5) : 600 et 500 sont
            // donc les deux bronzes, l'or (400) reste pour les accents.
            // Les gris sont les bruns AMANI, du fond de page (50) au texte (800).
            ->colors([
                'primary' => [
                    50 => '#fbf6ee', 100 => '#f5e8d7', 200 => '#eadcc4', 300 => '#dcc386',
                    400 => '#c6a15b', 500 => '#9c6b3f', 600 => '#85552c', 700 => '#734826',
                    800 => '#5e3b20', 900 => '#46301c', 950 => '#2e1d12',
                ],
                'gray' => [
                    50 => '#fcf8f3', 100 => '#f5eee4', 200 => '#eadccb', 300 => '#d6c3ae',
                    400 => '#a8927e', 500 => '#7a6555', 600 => '#6f5b4c', 700 => '#5a4738',
                    800 => '#493729', 900 => '#3a2b20', 950 => '#2a1f17',
                ],
                'success' => Color::hex('#2f6b4a'),
                'warning' => Color::hex('#8f6418'),
                'danger' => Color::hex('#a13d33'),
                'info' => Color::hex('#6f5b4c'),
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
