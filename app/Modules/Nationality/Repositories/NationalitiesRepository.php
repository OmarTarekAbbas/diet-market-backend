<?php

namespace App\Modules\Nationality\Repositories;

use App\Modules\Nationality\Models\Nationality as Model;
use App\Modules\Nationality\Filters\Nationality as Filter;
use App\Modules\Nationality\Resources\Nationality as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class NationalitiesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'nationalities';

    /**
     * Model class name
     *
     * @const string
     */
    const MODEL = Model::class;

    /**
     * Resource class name
     *
     * @const string
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['code', 'country_en', 'country_ar', 'nationality_en', 'natianality_ar'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = [];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = [];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel=>=>class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel=>=>class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [];

    /**
     * Set of the parents repositories of current repo
     *
     * @const array
     */
    const CHILD_OF = [];

    /**
     * Set of the children repositories of current repo
     *
     * @const array
     */
    const PARENT_OF = [];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [
        Filter::class,
    ];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = false;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
    }

    

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
    }

    

    /**
     * Method createNationality
     *
     * @return void
     */
    public function createNationality()
    {
        $nationalitys = [
            [
                "code" => "AF",
                "country_en" => " Afghanistan",
                "country_ar" => "أفغانستان",
                "nationality_en" => "Afghan",
                "natianality_ar" => "أفغانستاني",
            ],
            [
                "code" => "AL",
                "country_en" => " Albania",
                "country_ar" => "ألبانيا",
                "nationality_en" => "Albanian",
                "natianality_ar" => "ألباني",
            ],
            [
                "code" => "AX",
                "country_en" => " Aland Islands",
                "country_ar" => "جزر آلاند",
                "nationality_en" => "Aland Islander",
                "natianality_ar" => "آلاندي",
            ],
            [
                "code" => "DZ",
                "country_en" => " Algeria",
                "country_ar" => "الجزائر",
                "nationality_en" => "Algerian",
                "natianality_ar" => "جزائري",
            ],
            [
                "code" => "AS",
                "country_en" => " American Samoa",
                "country_ar" => "ساموا-الأمريكي",
                "nationality_en" => "American Samoan",
                "natianality_ar" => "أمريكي سامواني",
            ],
            [
                "code" => "AD",
                "country_en" => " Andorra",
                "country_ar" => "أندورا",
                "nationality_en" => "Andorran",
                "natianality_ar" => "أندوري",
            ],
            [
                "code" => "AO",
                "country_en" => " Angola",
                "country_ar" => "أنغولا",
                "nationality_en" => "Angolan",
                "natianality_ar" => "أنقولي",
            ],
            [
                "code" => "AI",
                "country_en" => " Anguilla",
                "country_ar" => "أنغويلا",
                "nationality_en" => "Anguillan",
                "natianality_ar" => "أنغويلي",
            ],
            [
                "code" => "AQ",
                "country_en" => " Antarctica",
                "country_ar" => "أنتاركتيكا",
                "nationality_en" => "Antarctican",
                "natianality_ar" => "أنتاركتيكي",
            ],
            [
                "code" => "AG",
                "country_en" => " Antigua and Barbuda",
                "country_ar" => "أنتيغوا وبربودا",
                "nationality_en" => "Antiguan",
                "natianality_ar" => "بربودي",
            ],
            [
                "code" => "AR",
                "country_en" => " Argentina",
                "country_ar" => "الأرجنتين",
                "nationality_en" => "Argentinian",
                "natianality_ar" => "أرجنتيني",
            ],
            [
                "code" => "AM",
                "country_en" => " Armenia",
                "country_ar" => "أرمينيا",
                "nationality_en" => "Armenian",
                "natianality_ar" => "أرميني",
            ],
            [
                "code" => "AW",
                "country_en" => " Aruba",
                "country_ar" => "أروبه",
                "nationality_en" => "Aruban",
                "natianality_ar" => "أوروبهيني",
            ],
            [
                "code" => "AU",
                "country_en" => " Australia",
                "country_ar" => "أستراليا",
                "nationality_en" => "Australian",
                "natianality_ar" => "أسترالي",
            ],
            [
                "code" => "AT",
                "country_en" => " Austria",
                "country_ar" => "النمسا",
                "nationality_en" => "Austrian",
                "natianality_ar" => "نمساوي",
            ],
            [
                "code" => "AZ",
                "country_en" => " Azerbaijan",
                "country_ar" => "أذربيجان",
                "nationality_en" => "Azerbaijani",
                "natianality_ar" => "أذربيجاني",
            ],
            [
                "code" => "BS",
                "country_en" => " Bahamas",
                "country_ar" => "الباهاماس",
                "nationality_en" => "Bahamian",
                "natianality_ar" => "باهاميسي",
            ],
            [
                "code" => "BH",
                "country_en" => " Bahrain",
                "country_ar" => "البحرين",
                "nationality_en" => "Bahraini",
                "natianality_ar" => "بحريني",
            ],
            [
                "code" => "BD",
                "country_en" => " Bangladesh",
                "country_ar" => "بنغلاديش",
                "nationality_en" => "Bangladeshi",
                "natianality_ar" => "بنغلاديشي",
            ],
            [
                "code" => "BB",
                "country_en" => " Barbados",
                "country_ar" => "بربادوس",
                "nationality_en" => "Barbadian",
                "natianality_ar" => "بربادوسي",
            ],
            [
                "code" => "BY",
                "country_en" => " Belarus",
                "country_ar" => "روسيا البيضاء",
                "nationality_en" => "Belarusian",
                "natianality_ar" => "روسي",
            ],
            [
                "code" => "BE",
                "country_en" => " Belgium",
                "country_ar" => "بلجيكا",
                "nationality_en" => "Belgian",
                "natianality_ar" => "بلجيكي",
            ],
            [
                "code" => "BZ",
                "country_en" => " Belize",
                "country_ar" => "بيليز",
                "nationality_en" => "Belizean",
                "natianality_ar" => "بيليزي",
            ],
            [
                "code" => "BJ",
                "country_en" => " Benin",
                "country_ar" => "بنين",
                "nationality_en" => "Beninese",
                "natianality_ar" => "بنيني",
            ],
            [
                "code" => "BL",
                "country_en" => " Saint Barthelemy",
                "country_ar" => "سان بارتيلمي",
                "nationality_en" => "Saint Barthelmian",
                "natianality_ar" => "سان بارتيلمي",
            ],
            [
                "code" => "BM",
                "country_en" => " Bermuda",
                "country_ar" => "جزر برمودا",
                "nationality_en" => "Bermudan",
                "natianality_ar" => "برمودي",
            ],
            [
                "code" => "BT",
                "country_en" => " Bhutan",
                "country_ar" => "بوتان",
                "nationality_en" => "Bhutanese",
                "natianality_ar" => "بوتاني",
            ],
            [
                "code" => "BO",
                "country_en" => " Bolivia",
                "country_ar" => "بوليفيا",
                "nationality_en" => "Bolivian",
                "natianality_ar" => "بوليفي",
            ],
            [
                "code" => "BA",
                "country_en" => " Bosnia and Herzegovina",
                "country_ar" => "البوسنة و الهرسك",
                "nationality_en" => "Bosnian / Herzegovinian",
                "natianality_ar" => "بوسني/هرسكي",
            ],
            [
                "code" => "BW",
                "country_en" => " Botswana",
                "country_ar" => "بوتسوانا",
                "nationality_en" => "Botswanan",
                "natianality_ar" => "بوتسواني",
            ],
            [
                "code" => "BV",
                "country_en" => " Bouvet Island",
                "country_ar" => "جزيرة بوفيه",
                "nationality_en" => "Bouvetian",
                "natianality_ar" => "بوفيهي",
            ],
            [
                "code" => "BR",
                "country_en" => " Brazil",
                "country_ar" => "البرازيل",
                "nationality_en" => "Brazilian",
                "natianality_ar" => "برازيلي",
            ],
            [
                "code" => "IO",
                "country_en" => " British Indian Ocean Territory",
                "country_ar" => "إقليم المحيط الهندي البريطاني",
                "nationality_en" => "British Indian Ocean Territory",
                "natianality_ar" => "إقليم المحيط الهندي البريطاني",
            ],
            [
                "code" => "BN",
                "country_en" => " Brunei Darussalam",
                "country_ar" => "بروني",
                "nationality_en" => "Bruneian",
                "natianality_ar" => "بروني",
            ],
            [
                "code" => "BG",
                "country_en" => " Bulgaria",
                "country_ar" => "بلغاريا",
                "nationality_en" => "Bulgarian",
                "natianality_ar" => "بلغاري",
            ],
            [
                "code" => "BF",
                "country_en" => " Burkina Faso",
                "country_ar" => "بوركينا فاسو",
                "nationality_en" => "Burkinabe",
                "natianality_ar" => "بوركيني",
            ],
            [
                "code" => "BI",
                "country_en" => " Burundi",
                "country_ar" => "بوروندي",
                "nationality_en" => "Burundian",
                "natianality_ar" => "بورونيدي",
            ],
            [
                "code" => "KH",
                "country_en" => " Cambodia",
                "country_ar" => "كمبوديا",
                "nationality_en" => "Cambodian",
                "natianality_ar" => "كمبودي",
            ],
            [
                "code" => "CM",
                "country_en" => " Cameroon",
                "country_ar" => "كاميرون",
                "nationality_en" => "Cameroonian",
                "natianality_ar" => "كاميروني",
            ],
            [
                "code" => "CA",
                "country_en" => " Canada",
                "country_ar" => "كندا",
                "nationality_en" => "Canadian",
                "natianality_ar" => "كندي",
            ],
            [
                "code" => "CV",
                "country_en" => " Cape Verde",
                "country_ar" => "الرأس الأخضر",
                "nationality_en" => "Cape Verdean",
                "natianality_ar" => "الرأس الأخضر",
            ],
            [
                "code" => "KY",
                "country_en" => " Cayman Islands",
                "country_ar" => "جزر كايمان",
                "nationality_en" => "Caymanian",
                "natianality_ar" => "كايماني",
            ],
            [
                "code" => "CF",
                "country_en" => " Central African Republic",
                "country_ar" => "جمهورية أفريقيا الوسطى",
                "nationality_en" => "Central African",
                "natianality_ar" => "أفريقي",
            ],
            [
                "code" => "TD",
                "country_en" => " Chad",
                "country_ar" => "تشاد",
                "nationality_en" => "Chadian",
                "natianality_ar" => "تشادي",
            ],
            [
                "code" => "CL",
                "country_en" => " Chile",
                "country_ar" => "شيلي",
                "nationality_en" => "Chilean",
                "natianality_ar" => "شيلي",
            ],
            [
                "code" => "CN",
                "country_en" => " China",
                "country_ar" => "الصين",
                "nationality_en" => "Chinese",
                "natianality_ar" => "صيني",
            ],
            [
                "code" => "CX",
                "country_en" => " Christmas Island",
                "country_ar" => "جزيرة عيد الميلاد",
                "nationality_en" => "Christmas Islander",
                "natianality_ar" => "جزيرة عيد الميلاد",
            ],
            [
                "code" => "CC",
                "country_en" => " Cocos (Keeling) Islands",
                "country_ar" => "جزر كوكوس",
                "nationality_en" => "Cocos Islander",
                "natianality_ar" => "جزر كوكوس",
            ],
            [
                "code" => "CO",
                "country_en" => " Colombia",
                "country_ar" => "كولومبيا",
                "nationality_en" => "Colombian",
                "natianality_ar" => "كولومبي",
            ],
            [
                "code" => "KM",
                "country_en" => " Comoros",
                "country_ar" => "جزر القمر",
                "nationality_en" => "Comorian",
                "natianality_ar" => "جزر القمر",
            ],
            [
                "code" => "CG",
                "country_en" => " Congo",
                "country_ar" => "الكونغو",
                "nationality_en" => "Congolese",
                "natianality_ar" => "كونغي",
            ],
            [
                "code" => "CK",
                "country_en" => " Cook Islands",
                "country_ar" => "جزر كوك",
                "nationality_en" => "Cook Islander",
                "natianality_ar" => "جزر كوك",
            ],
            [
                "code" => "CR",
                "country_en" => " Costa Rica",
                "country_ar" => "كوستاريكا",
                "nationality_en" => "Costa Rican",
                "natianality_ar" => "كوستاريكي",
            ],
            [
                "code" => "HR",
                "country_en" => " Croatia",
                "country_ar" => "كرواتيا",
                "nationality_en" => "Croatian",
                "natianality_ar" => "كوراتي",
            ],
            [
                "code" => "CU",
                "country_en" => " Cuba",
                "country_ar" => "كوبا",
                "nationality_en" => "Cuban",
                "natianality_ar" => "كوبي",
            ],
            [
                "code" => "CY",
                "country_en" => " Cyprus",
                "country_ar" => "قبرص",
                "nationality_en" => "Cypriot",
                "natianality_ar" => "قبرصي",
            ],
            [
                "code" => "CW",
                "country_en" => " Curaçao",
                "country_ar" => "كوراساو",
                "nationality_en" => "Curacian",
                "natianality_ar" => "كوراساوي",
            ],
            [
                "code" => "CZ",
                "country_en" => " Czech Republic",
                "country_ar" => "الجمهورية التشيكية",
                "nationality_en" => "Czech",
                "natianality_ar" => "تشيكي",
            ],
            [
                "code" => "DK",
                "country_en" => " Denmark",
                "country_ar" => "الدانمارك",
                "nationality_en" => "Danish",
                "natianality_ar" => "دنماركي",
            ],
            [
                "code" => "DJ",
                "country_en" => " Djibouti",
                "country_ar" => "جيبوتي",
                "nationality_en" => "Djiboutian",
                "natianality_ar" => "جيبوتي",
            ],
            [
                "code" => "DM",
                "country_en" => " Dominica",
                "country_ar" => "دومينيكا",
                "nationality_en" => "Dominican",
                "natianality_ar" => "دومينيكي",
            ],
            [
                "code" => "DO",
                "country_en" => " Dominican Republic",
                "country_ar" => "الجمهورية الدومينيكية",
                "nationality_en" => "Dominican",
                "natianality_ar" => "دومينيكي",
            ],
            [
                "code" => "EC",
                "country_en" => " Ecuador",
                "country_ar" => "إكوادور",
                "nationality_en" => "Ecuadorian",
                "natianality_ar" => "إكوادوري",
            ],
            [
                "code" => "EG",
                "country_en" => " Egypt",
                "country_ar" => "مصر",
                "nationality_en" => "Egyptian",
                "natianality_ar" => "مصري",
            ],
            [
                "code" => "SV",
                "country_en" => " El Salvador",
                "country_ar" => "إلسلفادور",
                "nationality_en" => "Salvadoran",
                "natianality_ar" => "سلفادوري",
            ],
            [
                "code" => "GQ",
                "country_en" => " Equatorial Guinea",
                "country_ar" => "غينيا الاستوائي",
                "nationality_en" => "Equatorial Guinean",
                "natianality_ar" => "غيني",
            ],
            [
                "code" => "ER",
                "country_en" => " Eritrea",
                "country_ar" => "إريتريا",
                "nationality_en" => "Eritrean",
                "natianality_ar" => "إريتيري",
            ],
            [
                "code" => "EE",
                "country_en" => " Estonia",
                "country_ar" => "استونيا",
                "nationality_en" => "Estonian",
                "natianality_ar" => "استوني",
            ],
            [
                "code" => "ET",
                "country_en" => " Ethiopia",
                "country_ar" => "أثيوبيا",
                "nationality_en" => "Ethiopian",
                "natianality_ar" => "أثيوبي",
            ],
            [
                "code" => "FK",
                "country_en" => " Falkland Islands (Malvinas)",
                "country_ar" => "جزر فوكلاند",
                "nationality_en" => "Falkland Islander",
                "natianality_ar" => "فوكلاندي",
            ],
            [
                "code" => "FO",
                "country_en" => " Faroe Islands",
                "country_ar" => "جزر فارو",
                "nationality_en" => "Faroese",
                "natianality_ar" => "جزر فارو",
            ],
            [
                "code" => "FJ",
                "country_en" => " Fiji",
                "country_ar" => "فيجي",
                "nationality_en" => "Fijian",
                "natianality_ar" => "فيجي",
            ],
            [
                "code" => "FI",
                "country_en" => " Finland",
                "country_ar" => "فنلندا",
                "nationality_en" => "Finnish",
                "natianality_ar" => "فنلندي",
            ],
            [
                "code" => "FR",
                "country_en" => " France",
                "country_ar" => "فرنسا",
                "nationality_en" => "French",
                "natianality_ar" => "فرنسي",
            ],
            [
                "code" => "GF",
                "country_en" => " French Guiana",
                "country_ar" => "غويانا الفرنسية",
                "nationality_en" => "French Guianese",
                "natianality_ar" => "غويانا الفرنسية",
            ],
            [
                "code" => "PF",
                "country_en" => " French Polynesia",
                "country_ar" => "بولينيزيا الفرنسية",
                "nationality_en" => "French Polynesian",
                "natianality_ar" => "بولينيزيي",
            ],
            [
                "code" => "TF",
                "country_en" => " French Southern and Antarctic Lands",
                "country_ar" => "أراض فرنسية جنوبية وأنتارتيكية",
                "nationality_en" => "French",
                "natianality_ar" => "أراض فرنسية جنوبية وأنتارتيكية",
            ],
            [
                "code" => "GA",
                "country_en" => " Gabon",
                "country_ar" => "الغابون",
                "nationality_en" => "Gabonese",
                "natianality_ar" => "غابوني",
            ],
            [
                "code" => "GM",
                "country_en" => " Gambia",
                "country_ar" => "غامبيا",
                "nationality_en" => "Gambian",
                "natianality_ar" => "غامبي",
            ],
            [
                "code" => "GE",
                "country_en" => " Georgia",
                "country_ar" => "جيورجيا",
                "nationality_en" => "Georgian",
                "natianality_ar" => "جيورجي",
            ],
            [
                "code" => "DE",
                "country_en" => " Germany",
                "country_ar" => "ألمانيا",
                "nationality_en" => "German",
                "natianality_ar" => "ألماني",
            ],
            [
                "code" => "GH",
                "country_en" => " Ghana",
                "country_ar" => "غانا",
                "nationality_en" => "Ghanaian",
                "natianality_ar" => "غاني",
            ],
            [
                "code" => "GI",
                "country_en" => " Gibraltar",
                "country_ar" => "جبل طارق",
                "nationality_en" => "Gibraltar",
                "natianality_ar" => "جبل طارق",
            ],
            [
                "code" => "GG",
                "country_en" => " Guernsey",
                "country_ar" => "غيرنزي",
                "nationality_en" => "Guernsian",
                "natianality_ar" => "غيرنزي",
            ],
            [
                "code" => "GR",
                "country_en" => " Greece",
                "country_ar" => "اليونان",
                "nationality_en" => "Greek",
                "natianality_ar" => "يوناني",
            ],
            [
                "code" => "GL",
                "country_en" => " Greenland",
                "country_ar" => "جرينلاند",
                "nationality_en" => "Greenlandic",
                "natianality_ar" => "جرينلاندي",
            ],
            [
                "code" => "GD",
                "country_en" => " Grenada",
                "country_ar" => "غرينادا",
                "nationality_en" => "Grenadian",
                "natianality_ar" => "غرينادي",
            ],
            [
                "code" => "GP",
                "country_en" => " Guadeloupe",
                "country_ar" => "جزر جوادلوب",
                "nationality_en" => "Guadeloupe",
                "natianality_ar" => "جزر جوادلوب",
            ],
            [
                "code" => "GU",
                "country_en" => " Guam",
                "country_ar" => "جوام",
                "nationality_en" => "Guamanian",
                "natianality_ar" => "جوامي",
            ],
            [
                "code" => "GT",
                "country_en" => " Guatemala",
                "country_ar" => "غواتيمال",
                "nationality_en" => "Guatemalan",
                "natianality_ar" => "غواتيمالي",
            ],
            [
                "code" => "GN",
                "country_en" => " Guinea",
                "country_ar" => "غينيا",
                "nationality_en" => "Guinean",
                "natianality_ar" => "غيني",
            ],
            [
                "code" => "GW",
                "country_en" => " Guinea-Bissau",
                "country_ar" => "غينيا-بيساو",
                "nationality_en" => "Guinea-Bissauan",
                "natianality_ar" => "غيني",
            ],
            [
                "code" => "GY",
                "country_en" => " Guyana",
                "country_ar" => "غيانا",
                "nationality_en" => "Guyanese",
                "natianality_ar" => "غياني",
            ],
            [
                "code" => "HT",
                "country_en" => " Haiti",
                "country_ar" => "هايتي",
                "nationality_en" => "Haitian",
                "natianality_ar" => "هايتي",
            ],
            [
                "code" => "HM",
                "country_en" => " Heard and Mc Donald Islands",
                "country_ar" => "جزيرة هيرد وجزر ماكدونالد",
                "nationality_en" => "Heard and Mc Donald Islanders",
                "natianality_ar" => "جزيرة هيرد وجزر ماكدونالد",
            ],
            [
                "code" => "HN",
                "country_en" => " Honduras",
                "country_ar" => "هندوراس",
                "nationality_en" => "Honduran",
                "natianality_ar" => "هندوراسي",
            ],
            [
                "code" => "HK",
                "country_en" => " Hong Kong",
                "country_ar" => "هونغ كونغ",
                "nationality_en" => "Hongkongese",
                "natianality_ar" => "هونغ كونغي",
            ],
            [
                "code" => "HU",
                "country_en" => " Hungary",
                "country_ar" => "المجر",
                "nationality_en" => "Hungarian",
                "natianality_ar" => "مجري",
            ],
            [
                "code" => "IS",
                "country_en" => " Iceland",
                "country_ar" => "آيسلندا",
                "nationality_en" => "Icelandic",
                "natianality_ar" => "آيسلندي",
            ],
            [
                "code" => "IN",
                "country_en" => " India",
                "country_ar" => "الهند",
                "nationality_en" => "Indian",
                "natianality_ar" => "هندي",
            ],
            [
                "code" => "IM",
                "country_en" => " Isle of Man",
                "country_ar" => "جزيرة مان",
                "nationality_en" => "Manx",
                "natianality_ar" => "ماني",
            ],
            [
                "code" => "ID",
                "country_en" => " Indonesia",
                "country_ar" => "أندونيسيا",
                "nationality_en" => "Indonesian",
                "natianality_ar" => "أندونيسيي",
            ],
            [
                "code" => "IR",
                "country_en" => " Iran",
                "country_ar" => "إيران",
                "nationality_en" => "Iranian",
                "natianality_ar" => "إيراني",
            ],
            [
                "code" => "IQ",
                "country_en" => " Iraq",
                "country_ar" => "العراق",
                "nationality_en" => "Iraqi",
                "natianality_ar" => "عراقي",
            ],
            [
                "code" => "IE",
                "country_en" => " Ireland",
                "country_ar" => "إيرلندا",
                "nationality_en" => "Irish",
                "natianality_ar" => "إيرلندي",
            ],
            [
                "code" => "IT",
                "country_en" => " Italy",
                "country_ar" => "إيطاليا",
                "nationality_en" => "Italian",
                "natianality_ar" => "إيطالي",
            ],
            [
                "code" => "CI",
                "country_en" => " Ivory Coast",
                "country_ar" => "ساحل العاج",
                "nationality_en" => "Ivory Coastian",
                "natianality_ar" => "ساحل العاج",
            ],
            [
                "code" => "JE",
                "country_en" => " Jersey",
                "country_ar" => "جيرزي",
                "nationality_en" => "Jersian",
                "natianality_ar" => "جيرزي",
            ],
            [
                "code" => "JM",
                "country_en" => " Jamaica",
                "country_ar" => "جمايكا",
                "nationality_en" => "Jamaican",
                "natianality_ar" => "جمايكي",
            ],
            [
                "code" => "JP",
                "country_en" => " Japan",
                "country_ar" => "اليابان",
                "nationality_en" => "Japanese",
                "natianality_ar" => "ياباني",
            ],
            [
                "code" => "JO",
                "country_en" => " Jordan",
                "country_ar" => "الأردن",
                "nationality_en" => "Jordanian",
                "natianality_ar" => "أردني",
            ],
            [
                "code" => "KZ",
                "country_en" => " Kazakhstan",
                "country_ar" => "كازاخستان",
                "nationality_en" => "Kazakh",
                "natianality_ar" => "كازاخستاني",
            ],
            [
                "code" => "KE",
                "country_en" => " Kenya",
                "country_ar" => "كينيا",
                "nationality_en" => "Kenyan",
                "natianality_ar" => "كيني",
            ],
            [
                "code" => "KI",
                "country_en" => " Kiribati",
                "country_ar" => "كيريباتي",
                "nationality_en" => "I-Kiribati",
                "natianality_ar" => "كيريباتي",
            ],
            [
                "code" => "KP",
                "country_en" => " Korea(North Korea)",
                "country_ar" => "كوريا الشمالية",
                "nationality_en" => "North Korean",
                "natianality_ar" => "كوري",
            ],
            [
                "code" => "KR",
                "country_en" => " Korea(South Korea)",
                "country_ar" => "كوريا الجنوبية",
                "nationality_en" => "South Korean",
                "natianality_ar" => "كوري",
            ],
            [
                "code" => "XK",
                "country_en" => " Kosovo",
                "country_ar" => "كوسوفو",
                "nationality_en" => "Kosovar",
                "natianality_ar" => "كوسيفي",
            ],
            [
                "code" => "KW",
                "country_en" => " Kuwait",
                "country_ar" => "الكويت",
                "nationality_en" => "Kuwaiti",
                "natianality_ar" => "كويتي",
            ],
            [
                "code" => "KG",
                "country_en" => " Kyrgyzstan",
                "country_ar" => "قيرغيزستان",
                "nationality_en" => "Kyrgyzstani",
                "natianality_ar" => "قيرغيزستاني",
            ],
            [
                "code" => "LA",
                "country_en" => " Lao PDR",
                "country_ar" => "لاوس",
                "nationality_en" => "Laotian",
                "natianality_ar" => "لاوسي",
            ],
            [
                "code" => "LV",
                "country_en" => " Latvia",
                "country_ar" => "لاتفيا",
                "nationality_en" => "Latvian",
                "natianality_ar" => "لاتيفي",
            ],
            [
                "code" => "LB",
                "country_en" => " Lebanon",
                "country_ar" => "لبنان",
                "nationality_en" => "Lebanese",
                "natianality_ar" => "لبناني",
            ],
            [
                "code" => "LS",
                "country_en" => " Lesotho",
                "country_ar" => "ليسوتو",
                "nationality_en" => "Basotho",
                "natianality_ar" => "ليوسيتي",
            ],
            [
                "code" => "LR",
                "country_en" => " Liberia",
                "country_ar" => "ليبيريا",
                "nationality_en" => "Liberian",
                "natianality_ar" => "ليبيري",
            ],
            [
                "code" => "LY",
                "country_en" => " Libya",
                "country_ar" => "ليبيا",
                "nationality_en" => "Libyan",
                "natianality_ar" => "ليبي",
            ],
            [
                "code" => "LI",
                "country_en" => " Liechtenstein",
                "country_ar" => "ليختنشتين",
                "nationality_en" => "Liechtenstein",
                "natianality_ar" => "ليختنشتيني",
            ],
            [
                "code" => "LT",
                "country_en" => " Lithuania",
                "country_ar" => "لتوانيا",
                "nationality_en" => "Lithuanian",
                "natianality_ar" => "لتوانيي",
            ],
            [
                "code" => "LU",
                "country_en" => " Luxembourg",
                "country_ar" => "لوكسمبورغ",
                "nationality_en" => "Luxembourger",
                "natianality_ar" => "لوكسمبورغي",
            ],
            [
                "code" => "LK",
                "country_en" => " Sri Lanka",
                "country_ar" => "سريلانكا",
                "nationality_en" => "Sri Lankian",
                "natianality_ar" => "سريلانكي",
            ],
            [
                "code" => "MO",
                "country_en" => " Macau",
                "country_ar" => "ماكاو",
                "nationality_en" => "Macanese",
                "natianality_ar" => "ماكاوي",
            ],
            [
                "code" => "MK",
                "country_en" => " Macedonia",
                "country_ar" => "مقدونيا",
                "nationality_en" => "Macedonian",
                "natianality_ar" => "مقدوني",
            ],
            [
                "code" => "MG",
                "country_en" => " Madagascar",
                "country_ar" => "مدغشقر",
                "nationality_en" => "Malagasy",
                "natianality_ar" => "مدغشقري",
            ],
            [
                "code" => "MW",
                "country_en" => " Malawi",
                "country_ar" => "مالاوي",
                "nationality_en" => "Malawian",
                "natianality_ar" => "مالاوي",
            ],
            [
                "code" => "MY",
                "country_en" => " Malaysia",
                "country_ar" => "ماليزيا",
                "nationality_en" => "Malaysian",
                "natianality_ar" => "ماليزي",
            ],
            [
                "code" => "MV",
                "country_en" => " Maldives",
                "country_ar" => "المالديف",
                "nationality_en" => "Maldivian",
                "natianality_ar" => "مالديفي",
            ],
            [
                "code" => "ML",
                "country_en" => " Mali",
                "country_ar" => "مالي",
                "nationality_en" => "Malian",
                "natianality_ar" => "مالي",
            ],
            [
                "code" => "MT",
                "country_en" => " Malta",
                "country_ar" => "مالطا",
                "nationality_en" => "Maltese",
                "natianality_ar" => "مالطي",
            ],
            [
                "code" => "MH",
                "country_en" => " Marshall Islands",
                "country_ar" => "جزر مارشال",
                "nationality_en" => "Marshallese",
                "natianality_ar" => "مارشالي",
            ],
            [
                "code" => "MQ",
                "country_en" => " Martinique",
                "country_ar" => "مارتينيك",
                "nationality_en" => "Martiniquais",
                "natianality_ar" => "مارتينيكي",
            ],
            [
                "code" => "MR",
                "country_en" => " Mauritania",
                "country_ar" => "موريتانيا",
                "nationality_en" => "Mauritanian",
                "natianality_ar" => "موريتانيي",
            ],
            [
                "code" => "MU",
                "country_en" => " Mauritius",
                "country_ar" => "موريشيوس",
                "nationality_en" => "Mauritian",
                "natianality_ar" => "موريشيوسي",
            ],
            [
                "code" => "YT",
                "country_en" => " Mayotte",
                "country_ar" => "مايوت",
                "nationality_en" => "Mahoran",
                "natianality_ar" => "مايوتي",
            ],
            [
                "code" => "MX",
                "country_en" => " Mexico",
                "country_ar" => "المكسيك",
                "nationality_en" => "Mexican",
                "natianality_ar" => "مكسيكي",
            ],
            [
                "code" => "FM",
                "country_en" => " Micronesia",
                "country_ar" => "مايكرونيزيا",
                "nationality_en" => "Micronesian",
                "natianality_ar" => "مايكرونيزيي",
            ],
            [
                "code" => "MD",
                "country_en" => " Moldova",
                "country_ar" => "مولدافيا",
                "nationality_en" => "Moldovan",
                "natianality_ar" => "مولديفي",
            ],
            [
                "code" => "MC",
                "country_en" => " Monaco",
                "country_ar" => "موناكو",
                "nationality_en" => "Monacan",
                "natianality_ar" => "مونيكي",
            ],
            [
                "code" => "MN",
                "country_en" => " Mongolia",
                "country_ar" => "منغوليا",
                "nationality_en" => "Mongolian",
                "natianality_ar" => "منغولي",
            ],
            [
                "code" => "ME",
                "country_en" => " Montenegro",
                "country_ar" => "الجبل الأسود",
                "nationality_en" => "Montenegrin",
                "natianality_ar" => "الجبل الأسود",
            ],
            [
                "code" => "MS",
                "country_en" => " Montserrat",
                "country_ar" => "مونتسيرات",
                "nationality_en" => "Montserratian",
                "natianality_ar" => "مونتسيراتي",
            ],
            [
                "code" => "MA",
                "country_en" => " Morocco",
                "country_ar" => "المغرب",
                "nationality_en" => "Moroccan",
                "natianality_ar" => "مغربي",
            ],
            [
                "code" => "MZ",
                "country_en" => " Mozambique",
                "country_ar" => "موزمبيق",
                "nationality_en" => "Mozambican",
                "natianality_ar" => "موزمبيقي",
            ],
            [
                "code" => "MM",
                "country_en" => " Myanmar",
                "country_ar" => "ميانمار",
                "nationality_en" => "Myanmarian",
                "natianality_ar" => "ميانماري",
            ],
            [
                "code" => "NA",
                "country_en" => " Namibia",
                "country_ar" => "ناميبيا",
                "nationality_en" => "Namibian",
                "natianality_ar" => "ناميبي",
            ],
            [
                "code" => "NR",
                "country_en" => " Nauru",
                "country_ar" => "نورو",
                "nationality_en" => "Nauruan",
                "natianality_ar" => "نوري",
            ],
            [
                "code" => "NP",
                "country_en" => " Nepal",
                "country_ar" => "نيبال",
                "nationality_en" => "Nepalese",
                "natianality_ar" => "نيبالي",
            ],
            [
                "code" => "NL",
                "country_en" => " Netherlands",
                "country_ar" => "هولندا",
                "nationality_en" => "Dutch",
                "natianality_ar" => "هولندي",
            ],
            [
                "code" => "AN",
                "country_en" => " Netherlands Antilles",
                "country_ar" => "جزر الأنتيل الهولندي",
                "nationality_en" => "Dutch Antilier",
                "natianality_ar" => "هولندي",
            ],
            [
                "code" => "NC",
                "country_en" => " New Caledonia",
                "country_ar" => "كاليدونيا الجديدة",
                "nationality_en" => "New Caledonian",
                "natianality_ar" => "كاليدوني",
            ],
            [
                "code" => "NZ",
                "country_en" => " New Zealand",
                "country_ar" => "نيوزيلندا",
                "nationality_en" => "New Zealander",
                "natianality_ar" => "نيوزيلندي",
            ],
            [
                "code" => "NI",
                "country_en" => " Nicaragua",
                "country_ar" => "نيكاراجوا",
                "nationality_en" => "Nicaraguan",
                "natianality_ar" => "نيكاراجوي",
            ],
            [
                "code" => "NE",
                "country_en" => " Niger",
                "country_ar" => "النيجر",
                "nationality_en" => "Nigerien",
                "natianality_ar" => "نيجيري",
            ],
            [
                "code" => "NG",
                "country_en" => " Nigeria",
                "country_ar" => "نيجيريا",
                "nationality_en" => "Nigerian",
                "natianality_ar" => "نيجيري",
            ],
            [
                "code" => "NU",
                "country_en" => " Niue",
                "country_ar" => "ني",
                "nationality_en" => "Niuean",
                "natianality_ar" => "ني",
            ],
            [
                "code" => "NF",
                "country_en" => " Norfolk Island",
                "country_ar" => "جزيرة نورفولك",
                "nationality_en" => "Norfolk Islander",
                "natianality_ar" => "نورفوليكي",
            ],
            [
                "code" => "MP",
                "country_en" => " Northern Mariana Islands",
                "country_ar" => "جزر ماريانا الشمالية",
                "nationality_en" => "Northern Marianan",
                "natianality_ar" => "ماريني",
            ],
            [
                "code" => "NO",
                "country_en" => " Norway",
                "country_ar" => "النرويج",
                "nationality_en" => "Norwegian",
                "natianality_ar" => "نرويجي",
            ],
            [
                "code" => "OM",
                "country_en" => " Oman",
                "country_ar" => "عمان",
                "nationality_en" => "Omani",
                "natianality_ar" => "عماني",
            ],
            [
                "code" => "PK",
                "country_en" => " Pakistan",
                "country_ar" => "باكستان",
                "nationality_en" => "Pakistani",
                "natianality_ar" => "باكستاني",
            ],
            [
                "code" => "PW",
                "country_en" => " Palau",
                "country_ar" => "بالاو",
                "nationality_en" => "Palauan",
                "natianality_ar" => "بالاوي",
            ],
            [
                "code" => "PS",
                "country_en" => " Palestine",
                "country_ar" => "فلسطين",
                "nationality_en" => "Palestinian",
                "natianality_ar" => "فلسطيني",
            ],
            [
                "code" => "PA",
                "country_en" => " Panama",
                "country_ar" => "بنما",
                "nationality_en" => "Panamanian",
                "natianality_ar" => "بنمي",
            ],
            [
                "code" => "PG",
                "country_en" => " Papua New Guinea",
                "country_ar" => "بابوا غينيا الجديدة",
                "nationality_en" => "Papua New Guinean",
                "natianality_ar" => "بابوي",
            ],
            [
                "code" => "PY",
                "country_en" => " Paraguay",
                "country_ar" => "باراغواي",
                "nationality_en" => "Paraguayan",
                "natianality_ar" => "بارغاوي",
            ],
            [
                "code" => "PE",
                "country_en" => " Peru",
                "country_ar" => "بيرو",
                "nationality_en" => "Peruvian",
                "natianality_ar" => "بيري",
            ],
            [
                "code" => "PH",
                "country_en" => " Philippines",
                "country_ar" => "الفليبين",
                "nationality_en" => "Filipino",
                "natianality_ar" => "فلبيني",
            ],
            [
                "code" => "PN",
                "country_en" => " Pitcairn",
                "country_ar" => "بيتكيرن",
                "nationality_en" => "Pitcairn Islander",
                "natianality_ar" => "بيتكيرني",
            ],
            [
                "code" => "PL",
                "country_en" => " Poland",
                "country_ar" => "بولونيا",
                "nationality_en" => "Polish",
                "natianality_ar" => "بوليني",
            ],
            [
                "code" => "PT",
                "country_en" => " Portugal",
                "country_ar" => "البرتغال",
                "nationality_en" => "Portuguese",
                "natianality_ar" => "برتغالي",
            ],
            [
                "code" => "PR",
                "country_en" => " Puerto Rico",
                "country_ar" => "بورتو ريكو",
                "nationality_en" => "Puerto Rican",
                "natianality_ar" => "بورتي",
            ],
            [
                "code" => "QA",
                "country_en" => " Qatar",
                "country_ar" => "قطر",
                "nationality_en" => "Qatari",
                "natianality_ar" => "قطري",
            ],
            [
                "code" => "RE",
                "country_en" => " Reunion Island",
                "country_ar" => "ريونيون",
                "nationality_en" => "Reunionese",
                "natianality_ar" => "ريونيوني",
            ],
            [
                "code" => "RO",
                "country_en" => " Romania",
                "country_ar" => "رومانيا",
                "nationality_en" => "Romanian",
                "natianality_ar" => "روماني",
            ],
            [
                "code" => "RU",
                "country_en" => " Russian",
                "country_ar" => "روسيا",
                "nationality_en" => "Russian",
                "natianality_ar" => "روسي",
            ],
            [
                "code" => "RW",
                "country_en" => " Rwanda",
                "country_ar" => "رواندا",
                "nationality_en" => "Rwandan",
                "natianality_ar" => "رواندا",
            ],
            [
                "code" => "KN",
                "country_en" => " Saint Kitts and Nevis",
                "country_ar" => "سانت كيتس ونيفس",
                "nationality_en" => "",
                "natianality_ar" => "Kittitian/Nevisian",
            ],
            [
                "code" => "MF",
                "country_en" => " Saint Martin (French part)",
                "country_ar" => "ساينت مارتن فرنسي",
                "nationality_en" => "St. Martian(French)",
                "natianality_ar" => "ساينت مارتني فرنسي",
            ],
            [
                "code" => "SX",
                "country_en" => " Sint Maarten (Dutch part)",
                "country_ar" => "ساينت مارتن هولندي",
                "nationality_en" => "St. Martian(Dutch)",
                "natianality_ar" => "ساينت مارتني هولندي",
            ],
            [
                "code" => "LC",
                "country_en" => " Saint Pierre and Miquelon",
                "country_ar" => "سان بيير وميكلون",
                "nationality_en" => "St. Pierre and Miquelon",
                "natianality_ar" => "سان بيير وميكلوني",
            ],
            [
                "code" => "VC",
                "country_en" => " Saint Vincent and the Grenadines",
                "country_ar" => "سانت فنسنت وجزر غرينادين",
                "nationality_en" => "Saint Vincent and the Grenadines",
                "natianality_ar" => "سانت فنسنت وجزر غرينادين",
            ],
            [
                "code" => "WS",
                "country_en" => " Samoa",
                "country_ar" => "ساموا",
                "nationality_en" => "Samoan",
                "natianality_ar" => "ساموي",
            ],
            [
                "code" => "SM",
                "country_en" => " San Marino",
                "country_ar" => "سان مارينو",
                "nationality_en" => "Sammarinese",
                "natianality_ar" => "ماريني",
            ],
            [
                "code" => "ST",
                "country_en" => " Sao Tome and Principe",
                "country_ar" => "ساو تومي وبرينسيبي",
                "nationality_en" => "Sao Tomean",
                "natianality_ar" => "ساو تومي وبرينسيبي",
            ],
            [
                "code" => "SA",
                "country_en" => " Saudi Arabia",
                "country_ar" => "المملكة العربية السعودية",
                "nationality_en" => "Saudi Arabian",
                "natianality_ar" => "سعودي",
            ],
            [
                "code" => "SN",
                "country_en" => " Senegal",
                "country_ar" => "السنغال",
                "nationality_en" => "Senegalese",
                "natianality_ar" => "سنغالي",
            ],
            [
                "code" => "RS",
                "country_en" => " Serbia",
                "country_ar" => "صربيا",
                "nationality_en" => "Serbian",
                "natianality_ar" => "صربي",
            ],
            [
                "code" => "SC",
                "country_en" => " Seychelles",
                "country_ar" => "سيشيل",
                "nationality_en" => "Seychellois",
                "natianality_ar" => "سيشيلي",
            ],
            [
                "code" => "SL",
                "country_en" => " Sierra Leone",
                "country_ar" => "سيراليون",
                "nationality_en" => "Sierra Leonean",
                "natianality_ar" => "سيراليوني",
            ],
            [
                "code" => "SG",
                "country_en" => " Singapore",
                "country_ar" => "سنغافورة",
                "nationality_en" => "Singaporean",
                "natianality_ar" => "سنغافوري",
            ],
            [
                "code" => "SK",
                "country_en" => " Slovakia",
                "country_ar" => "سلوفاكيا",
                "nationality_en" => "Slovak",
                "natianality_ar" => "سولفاكي",
            ],
            [
                "code" => "SI",
                "country_en" => " Slovenia",
                "country_ar" => "سلوفينيا",
                "nationality_en" => "Slovenian",
                "natianality_ar" => "سولفيني",
            ],
            [
                "code" => "SB",
                "country_en" => " Solomon Islands",
                "country_ar" => "جزر سليمان",
                "nationality_en" => "Solomon Island",
                "natianality_ar" => "جزر سليمان",
            ],
            [
                "code" => "SO",
                "country_en" => " Somalia",
                "country_ar" => "الصومال",
                "nationality_en" => "Somali",
                "natianality_ar" => "صومالي",
            ],
            [
                "code" => "ZA",
                "country_en" => " South Africa",
                "country_ar" => "جنوب أفريقيا",
                "nationality_en" => "South African",
                "natianality_ar" => "أفريقي",
            ],
            [
                "code" => "GS",
                "country_en" => " South Georgia and the South Sandwich",
                "country_ar" => "المنطقة القطبية الجنوبية",
                "nationality_en" => "South Georgia and the South Sandwich",
                "natianality_ar" => "لمنطقة القطبية الجنوبية",
            ],
            [
                "code" => "SS",
                "country_en" => " South Sudan",
                "country_ar" => "السودان الجنوبي",
                "nationality_en" => "South Sudanese",
                "natianality_ar" => "سوادني جنوبي",
            ],
            [
                "code" => "ES",
                "country_en" => " Spain",
                "country_ar" => "إسبانيا",
                "nationality_en" => "Spanish",
                "natianality_ar" => "إسباني",
            ],
            [
                "code" => "SH",
                "country_en" => " Saint Helena",
                "country_ar" => "سانت هيلانة",
                "nationality_en" => "St. Helenian",
                "natianality_ar" => "هيلاني",
            ],
            [
                "code" => "SD",
                "country_en" => " Sudan",
                "country_ar" => "السودان",
                "nationality_en" => "Sudanese",
                "natianality_ar" => "سوداني",
            ],
            [
                "code" => "SR",
                "country_en" => " Suriname",
                "country_ar" => "سورينام",
                "nationality_en" => "Surinamese",
                "natianality_ar" => "سورينامي",
            ],
            [
                "code" => "SJ",
                "country_en" => " Svalbard and Jan Mayen",
                "country_ar" => "سفالبارد ويان ماين",
                "nationality_en" => "Svalbardian/Jan Mayenian",
                "natianality_ar" => "سفالبارد ويان ماين",
            ],
            [
                "code" => "SZ",
                "country_en" => " Swaziland",
                "country_ar" => "سوازيلند",
                "nationality_en" => "Swazi",
                "natianality_ar" => "سوازيلندي",
            ],
            [
                "code" => "SE",
                "country_en" => " Sweden",
                "country_ar" => "السويد",
                "nationality_en" => "Swedish",
                "natianality_ar" => "سويدي",
            ],
            [
                "code" => "CH",
                "country_en" => " Switzerland",
                "country_ar" => "سويسرا",
                "nationality_en" => "Swiss",
                "natianality_ar" => "سويسري",
            ],
            [
                "code" => "SY",
                "country_en" => " Syria",
                "country_ar" => "سوريا",
                "nationality_en" => "Syrian",
                "natianality_ar" => "سوري",
            ],
            [
                "code" => "TW",
                "country_en" => " Taiwan",
                "country_ar" => "تايوان",
                "nationality_en" => "Taiwanese",
                "natianality_ar" => "تايواني",
            ],
            [
                "code" => "TJ",
                "country_en" => " Tajikistan",
                "country_ar" => "طاجيكستان",
                "nationality_en" => "Tajikistani",
                "natianality_ar" => "طاجيكستاني",
            ],
            [
                "code" => "TZ",
                "country_en" => " Tanzania",
                "country_ar" => "تنزانيا",
                "nationality_en" => "Tanzanian",
                "natianality_ar" => "تنزانيي",
            ],
            [
                "code" => "TH",
                "country_en" => " Thailand",
                "country_ar" => "تايلندا",
                "nationality_en" => "Thai",
                "natianality_ar" => "تايلندي",
            ],
            [
                "code" => "TL",
                "country_en" => " Timor-Leste",
                "country_ar" => "تيمور الشرقية",
                "nationality_en" => "Timor-Lestian",
                "natianality_ar" => "تيموري",
            ],
            [
                "code" => "TG",
                "country_en" => " Togo",
                "country_ar" => "توغو",
                "nationality_en" => "Togolese",
                "natianality_ar" => "توغي",
            ],
            [
                "code" => "TK",
                "country_en" => " Tokelau",
                "country_ar" => "توكيلاو",
                "nationality_en" => "Tokelaian",
                "natianality_ar" => "توكيلاوي",
            ],
            [
                "code" => "TO",
                "country_en" => " Tonga",
                "country_ar" => "تونغا",
                "nationality_en" => "Tongan",
                "natianality_ar" => "تونغي",
            ],
            [
                "code" => "TT",
                "country_en" => " Trinidad and Tobago",
                "country_ar" => "ترينيداد وتوباغو",
                "nationality_en" => "Trinidadian/Tobagonian",
                "natianality_ar" => "ترينيداد وتوباغو",
            ],
            [
                "code" => "TN",
                "country_en" => " Tunisia",
                "country_ar" => "تونس",
                "nationality_en" => "Tunisian",
                "natianality_ar" => "تونسي",
            ],
            [
                "code" => "TR",
                "country_en" => " Turkey",
                "country_ar" => "تركيا",
                "nationality_en" => "Turkish",
                "natianality_ar" => "تركي",
            ],
            [
                "code" => "TM",
                "country_en" => " Turkmenistan",
                "country_ar" => "تركمانستان",
                "nationality_en" => "Turkmen",
                "natianality_ar" => "تركمانستاني",
            ],
            [
                "code" => "TC",
                "country_en" => " Turks and Caicos Islands",
                "country_ar" => "جزر توركس وكايكوس",
                "nationality_en" => "Turks and Caicos Islands",
                "natianality_ar" => "جزر توركس وكايكوس",
            ],
            [
                "code" => "TV",
                "country_en" => " Tuvalu",
                "country_ar" => "توفالو",
                "nationality_en" => "Tuvaluan",
                "natianality_ar" => "توفالي",
            ],
            [
                "code" => "UG",
                "country_en" => " Uganda",
                "country_ar" => "أوغندا",
                "nationality_en" => "Ugandan",
                "natianality_ar" => "أوغندي",
            ],
            [
                "code" => "UA",
                "country_en" => " Ukraine",
                "country_ar" => "أوكرانيا",
                "nationality_en" => "Ukrainian",
                "natianality_ar" => "أوكراني",
            ],
            [
                "code" => "AE",
                "country_en" => " United Arab Emirates",
                "country_ar" => "الإمارات العربية المتحدة",
                "nationality_en" => "Emirati",
                "natianality_ar" => "إماراتي",
            ],
            [
                "code" => "GB",
                "country_en" => " United Kingdom",
                "country_ar" => "المملكة المتحدة",
                "nationality_en" => "British",
                "natianality_ar" => "بريطاني",
            ],
            [
                "code" => "US",
                "country_en" => " United States",
                "country_ar" => "الولايات المتحدة",
                "nationality_en" => "American",
                "natianality_ar" => "أمريكي",
            ],
            [
                "code" => "UM",
                "country_en" => " US Minor Outlying Islands",
                "country_ar" => "قائمة الولايات والمناطق الأمريكية",
                "nationality_en" => "US Minor Outlying Islander",
                "natianality_ar" => "قائمة الولايات والمناطق الأمريكية",
            ],
            [
                "code" => "UY",
                "country_en" => " Uruguay",
                "country_ar" => "أورغواي",
                "nationality_en" => "Uruguayan",
                "natianality_ar" => "أورغواي",
            ],
            [
                "code" => "UZ",
                "country_en" => " Uzbekistan",
                "country_ar" => "أوزباكستان",
                "nationality_en" => "Uzbek",
                "natianality_ar" => "أوزباكستاني",
            ],
            [
                "code" => "VU",
                "country_en" => " Vanuatu",
                "country_ar" => "فانواتو",
                "nationality_en" => "Vanuatuan",
                "natianality_ar" => "فانواتي",
            ],
            [
                "code" => "VE",
                "country_en" => " Venezuela",
                "country_ar" => "فنزويلا",
                "nationality_en" => "Venezuelan",
                "natianality_ar" => "فنزويلي",
            ],
            [
                "code" => "VN",
                "country_en" => " Vietnam",
                "country_ar" => "فيتنام",
                "nationality_en" => "Vietnamese",
                "natianality_ar" => "فيتنامي",
            ],
            [
                "code" => "VI",
                "country_en" => " Virgin Islands (U.S.)",
                "country_ar" => "الجزر العذراء الأمريكي",
                "nationality_en" => "American Virgin Islander",
                "natianality_ar" => "الجزر العذراء الأمريكي",
            ],
            [
                "code" => "VA",
                "country_en" => " Vatican City",
                "country_ar" => "فنزويلا",
                "nationality_en" => "Vatican",
                "natianality_ar" => "فاتيكاني",
            ],
            [
                "code" => "WF",
                "country_en" => " Wallis and Futuna Islands",
                "country_ar" => "والس وفوتونا",
                "nationality_en" => "Wallisian/Futunan",
                "natianality_ar" => "فوتوني",
            ],
            [
                "code" => "EH",
                "country_en" => " Western Sahara",
                "country_ar" => "الصحراء الغربية",
                "nationality_en" => "Sahrawian",
                "natianality_ar" => "صحراوي",
            ],
            [
                "code" => "YE",
                "country_en" => " Yemen",
                "country_ar" => "اليمن",
                "nationality_en" => "Yemeni",
                "natianality_ar" => "يمني",
            ],
            [
                "code" => "ZM",
                "country_en" => " Zambia",
                "country_ar" => "زامبيا",
                "nationality_en" => "Zambian",
                "natianality_ar" => "زامبياني",
            ],
            [
                "code" => "ZW",
                "country_en" => " Zimbabwe",
                "country_ar" => "زمبابوي",
                "nationality_en" => "Zimbabwean",
                "natianality_ar" => "زمبابوي",
            ],

        ];
        foreach ($nationalitys as $key => $nationality) {
            repo('nationalities')->create([
                'code' => $nationality['code'],
                'country_en' => $nationality['country_en'],
                'country_ar' => $nationality['country_ar'],
                'nationality_en' => $nationality['nationality_en'],
                'natianality_ar' => $nationality['natianality_ar'],
            ]);
        }
    }
}
