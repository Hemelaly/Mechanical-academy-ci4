<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Certificate extends BaseConfig
{
    /**
     * ForÃ§a regenerar PDFs mesmo se jÃ¡ existirem (Ãºtil quando ajusta posiÃ§Ãµes/fonte).
     * Pode ser ativado via .env: certificate.forceRegenerate = true
     */
    public bool $forceRegenerate = false;
    /**
     * Prefixo do número do certificado (ex: MT = Mechanical Tecnologia)
     */
    public string $issuerCode = 'MT';

    /**
     * Director Geral (assinatura)
     */
    public string $directorName = '';
    public string $directorTitle = '';

    /**
     * No novo layout o campo do Director Geral foi removido.
     * Mantém false para não imprimir assinatura/nome do director.
     */
    public bool $showDirectorSignature = false;

    /**
     * Caminho (public/) opcional para imagem de assinatura do director.
     * Exemplo: 'assets/certificado/assinatura-diretor.png'
     */
    public string $directorSignaturePath = '';

    /**
     * Título/label do instrutor no certificado
     */
    public string $instructorTitle = 'Formador';

    /**
     * Caminho (public/) opcional para imagem de assinatura do instrutor.
     * Se vazio, mostra apenas a linha e o nome.
     */
    public string $instructorSignaturePath = '';

    /**
     * Fontes Google (fallback HTML/Dompdf) para nome e título do curso.
     */
    public string $studentNameFontCssUrl = 'https://fonts.googleapis.com/css2?family=Allura&family=Playfair+Display:ital@1&display=swap';

    /**
     * Template PDF base (public/) e posições em mm.
     * O ficheiro base fica em public/assets/certificado/certificado.pdf.
     */
    public string $templatePdfPath = 'assets/certificado/certificado.pdf';

    /**
     * URL pública da Mechanical Academy usada no QR Code.
     * Se ficar vazia, o sistema usa site_url().
     */
    public string $academyUrl = '';

    /**
     * Fonte TTF para o nome do estudante (caligrafia elegante).
     */
    public string $studentNameTtfPath = 'assets/fonts/Allura-Regular.ttf';

    /**
     * Fonte TTF para o título do curso (mesma caligrafia do nome do estudante).
     */
    public string $courseNameTtfPath = 'assets/fonts/Allura-Regular.ttf';

    /**
     * Fonte TTF para nomes nas assinaturas. Deve parecer assinatura manuscrita.
     */
    public string $signatureTtfPath = 'assets/fonts/MsMadi-Regular.ttf';

    /**
     * Exibe o código de verificação no certificado (opcional no novo layout).
     */
    public bool $showVerificationCode = false;

    /**
     * Exibe QR Code de verificação no certificado (opcional no novo layout).
     */
    public bool $showQrCode = false;

    /** @var array<string, array{x:float,y:float,w:float,h:float,size?:float,align?:string,bold?:bool,font?:string,type?:string,valign?:string}> */
    public array $templatePositions = [
        // Coordenadas calibradas para public/assets/certificado/certificado.pdf (210 x 148 mm).
        // y + h = posição da linha pontilhada; valign=B assenta o texto sobre a linha.

        // Data de conclusão — linha pontilhada superior esquerda (~y 31.5 mm).
        'concluded_date' => ['x' => 10.0, 'y' => 27.0, 'w' => 46.0, 'h' => 4.5, 'size' => 8.5, 'align' => 'C', 'valign' => 'B'],

        // Nome do formando — linha pontilhada central (~y 84.5 mm).
        'student_name' => ['x' => 37.0, 'y' => 76.0, 'w' => 136.0, 'h' => 8.5, 'size' => 24.0, 'align' => 'C', 'min_size' => 14.0, 'font_role' => 'student', 'valign' => 'B'],

        // Nome do curso — segunda linha pontilhada central (~y 108.5 mm).
        'course_name' => ['x' => 34.0, 'y' => 97.0, 'w' => 142.0, 'h' => 8.5, 'size' => 14.0, 'align' => 'C', 'min_size' => 10.0, 'font_role' => 'course', 'valign' => 'B'],

        // Formador — linha pontilhada inferior esquerda (~y 130.5 mm).
        'instructor_name' => ['x' => 44.0, 'y' => 123.0, 'w' => 52.0, 'h' => 7.5, 'size' => 11.0, 'align' => 'C', 'font_role' => 'signature', 'valign' => 'B'],

        // Alias mantido para compatibilidade com o serviço atual.
        'issued_date' => ['x' => 10.0, 'y' => 27.0, 'w' => 46.0, 'h' => 4.5, 'size' => 8.5, 'align' => 'C', 'valign' => 'B'],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->forceRegenerate = filter_var(env('certificate.forceRegenerate', $this->forceRegenerate), FILTER_VALIDATE_BOOL);
        $this->issuerCode = trim((string) env('certificate.issuerCode', $this->issuerCode));
        $this->directorName = trim((string) env('certificate.directorName', $this->directorName));
        $this->directorTitle = trim((string) env('certificate.directorTitle', $this->directorTitle));
        $this->showDirectorSignature = filter_var(env('certificate.showDirectorSignature', $this->showDirectorSignature), FILTER_VALIDATE_BOOL);
        $this->showVerificationCode = filter_var(env('certificate.showVerificationCode', $this->showVerificationCode), FILTER_VALIDATE_BOOL);
        $this->showQrCode = filter_var(env('certificate.showQrCode', $this->showQrCode), FILTER_VALIDATE_BOOL);
        $this->directorSignaturePath = trim((string) env('certificate.directorSignaturePath', $this->directorSignaturePath));
        $this->instructorTitle = trim((string) env('certificate.instructorTitle', $this->instructorTitle));
        $this->instructorSignaturePath = trim((string) env('certificate.instructorSignaturePath', $this->instructorSignaturePath));

        $this->studentNameFontCssUrl = trim((string) env('certificate.studentNameFontCssUrl', $this->studentNameFontCssUrl));
        $this->academyUrl = rtrim(trim((string) env('certificate.academyUrl', $this->academyUrl)), '/');
        $this->templatePdfPath = trim((string) env('certificate.templatePdfPath', $this->templatePdfPath));
        $this->studentNameTtfPath = trim((string) env('certificate.studentNameTtfPath', $this->studentNameTtfPath));
        $this->courseNameTtfPath = trim((string) env('certificate.courseNameTtfPath', $this->courseNameTtfPath));
        $this->signatureTtfPath = trim((string) env('certificate.signatureTtfPath', $this->signatureTtfPath));

        $positionsJson = trim((string) env('certificate.templatePositionsJson', ''));
        if ($positionsJson !== '') {
            $decoded = json_decode($positionsJson, true);
            if (is_array($decoded)) {
                $this->templatePositions = $decoded;
            }
        }
    }
}
