# Testy poprawek formularzy Filament

## Przegląd

Testy weryfikują wszystkie poprawki wprowadzone w branchu `poprawki`:
- Zmiany typów pól (TextInput -> Toggle, TextInput -> Select)
- Warunki widoczności pól (->live() + ->visible())
- Reorganizację zakładek i sekcji
- Dodanie i usunięcie pól
- Obsługa błędów przy tworzeniu rekordów (UniqueConstraintViolationException)
- Naprawa sekwencji PostgreSQL

## Uruchomienie testów

### Feature Tests (model-based)

```bash
php artisan test tests/Feature/Filament/ResourceFormFixesTest.php
```

**Oczekiwany wynik: 29 passed (94 assertions)**

### Browser Tests (Dusk - wymaga php artisan serve)

```bash
# Terminal 1: uruchom serwer
php artisan serve

# Terminal 2: uruchom testy Dusk
php artisan dusk tests/Browser/FormFixesBrowserTest.php
```

Konfiguracja w `.env.dusk.local`:
```
REMOTE_TEST_URL=http://127.0.0.1:8000
REMOTE_TEST_EMAIL=test@example.com
REMOTE_TEST_PASSWORD=password
```

## Pokrycie testami

### OCZYSZCZACZE (AirPurifier) - 4 testy
| Test | Poprawka |
|------|----------|
| `test_air_purifier_humidification_toggle_with_related_fields` | Toggle nawilżania z ->live() |
| `test_air_purifier_hygrostat_toggle_enables_range` | Hygrostat toggle z ->live() |
| `test_air_purifier_mesh_filter_boolean` | Label 'Filtr wstępny' |
| `test_air_purifier_ranking_fields` | Ranking w Performance tab |

### NAWILŻACZE (AirHumidifier) - 5 testów
| Test | Poprawka |
|------|----------|
| `test_air_humidifier_type_of_device_accepts_select_values` | TextInput -> Select |
| `test_air_humidifier_auto_mode_without_min_max` | Usunięcie auto_mode_min/max |
| `test_air_humidifier_ranking_in_basic_info` | Ranking w Podstawowe informacje |
| `test_air_humidifier_fan_volume_fields` | Fan volume w Wydajność |
| `test_air_humidifier_power_consumption_in_wydajnosc` | Pobór prądu w Wydajność |

### OSUSZACZE (Dehumidifier) - 3 testy
| Test | Poprawka |
|------|----------|
| `test_dehumidifier_higrostat_is_toggle` | TagsInput -> Toggle |
| `test_dehumidifier_higrostat_range_values` | Widoczność pól higrostatu |
| `test_dehumidifier_ranking_without_points` | Ranking bez _points, w Podstawowe |

### KLIMATYZATORY (AirConditioner) - 2 testy
| Test | Poprawka |
|------|----------|
| `test_air_conditioner_type_select_values` | TextInput -> Select (5 opcji) |
| `test_air_conditioner_ranking_without_points` | Usunięcie capability_points/profitability_points |

### ODKURZACZE (UprightVacuum) - 13 testów
| Test | Poprawka |
|------|----------|
| `test_upright_vacuum_type_select_values` | TextInput -> Select (6 opcji) |
| `test_upright_vacuum_power_supply_array` | power_supply jako array |
| `test_upright_vacuum_cable_length_with_sieciowe` | Widoczność kabla przy Sieciowe |
| `test_upright_vacuum_battery_change_select` | TextInput -> Select (3 opcje) |
| `test_upright_vacuum_battery_status_select` | TextInput -> Select (4 opcje) |
| `test_upright_vacuum_mopping_fields_are_toggles` | 4x TextInput -> Toggle |
| `test_upright_vacuum_type_of_washing_multi_select` | TextInput -> Select::multiple() |
| `test_upright_vacuum_mopping_time_numeric` | ->numeric()->suffix('min') |
| `test_upright_vacuum_filter_fields_are_toggles` | 8x TextInput -> Toggle |
| `test_upright_vacuum_brush_fields_are_toggles` | 7x TextInput -> Toggle |
| `test_upright_vacuum_charging_station_select` | TextInput -> Select (5 opcji) |
| `test_upright_vacuum_filtration_system_select` | TextInput -> Select (5 opcji) |
| `test_upright_vacuum_ranking_without_points` | Usunięcie capability_points/profitability_points |

### Infrastruktura - 2 testy
| Test | Poprawka |
|------|----------|
| `test_create_pages_use_error_handling_trait` | HandlesRecordCreationErrors trait |
| `test_sequence_fix_migration_exists` | Migracja sekwencji PostgreSQL |
