<?php
require("connection.php");

function myheader() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Site Examen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white text-center py-4 mb-4">
        <h1>Mon Site Examen</h1>
        <nav class="nav justify-content-center">
            <a class="nav-link text-white" href="index.php">Accueil</a>
            <a class="nav-link text-white" href="employes.php">Employés</a>
            <a class="nav-link text-white" href="contact.php">Contact</a>
        </nav>
    </header>
    <main class="container mb-5">
HTML;
}

function myfooter() {
    echo <<<HTML
    </main>
    <footer class="bg-dark text-white text-center py-3 fixed-bottom">
        &copy; 2025 - Mon Site Examen
    </footer>
    <script src="../assets/css/bootstrap-grid.min.css"></script>
</body>
</html>
HTML;
}



function name_manager()
{
    $sql = "SELECT d.dept_no, d.dept_name, e.last_name AS manager_last_name
            FROM departments d
            LEFT JOIN dept_manager dm ON d.dept_no = dm.dept_no AND dm.to_date = '9999-01-01'
            LEFT JOIN employees e ON dm.emp_no = e.emp_no";
    $result = mysqli_query(dbconnect(), $sql);
    return $result;
}

function name_employes()
{
    $connexion = dbconnect();
    if (isset($_GET['dept_no'])) {
        $dept_no = mysqli_real_escape_string($connexion, $_GET['dept_no']);
        $sql = "SELECT 
                    e.emp_no, 
                    e.first_name, 
                    e.last_name,
                    e.hire_date,
                    TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) AS age,
                    t.title
                FROM employees e
                JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date = '9999-01-01'
                LEFT JOIN titles t ON e.emp_no = t.emp_no AND t.to_date = '9999-01-01'
                WHERE de.dept_no = '$dept_no'
                ORDER BY e.last_name, e.first_name";
        $result = mysqli_query($connexion, $sql);
    } else {
        echo "Aucun département sélectionné.";
        exit;
    }
    return $result;
}

function ficheEmploye($emp_no) {
    $conn = dbconnect();
    
    $sql = "SELECT 
                e.emp_no,
                e.first_name,
                e.last_name,
                e.birth_date,
                e.gender,
                e.hire_date,
                TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) AS age,
                TIMESTAMPDIFF(YEAR, e.hire_date, CURDATE()) AS anciennete,
                d.dept_name,
                d.dept_no,
                t.title,
                s.salary,
                s.from_date as salary_from_date,
                s.to_date as salary_to_date
            FROM employees e
            LEFT JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date = '9999-01-01'
            LEFT JOIN departments d ON de.dept_no = d.dept_no
            LEFT JOIN titles t ON e.emp_no = t.emp_no AND t.to_date = '9999-01-01'
            LEFT JOIN salaries s ON e.emp_no = s.emp_no AND s.to_date = '9999-01-01'
            WHERE e.emp_no = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Erreur préparation requête ficheEmploye: " . mysqli_error($conn));
        return false;
    }
    
    $stmt->bind_param('i', $emp_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function getEmployeeHistory($emp_no) {
    $conn = dbconnect();
    
    $sql_dept = "SELECT d.dept_name, de.from_date, de.to_date 
                 FROM dept_emp de 
                 JOIN departments d ON de.dept_no = d.dept_no 
                 WHERE de.emp_no = ? 
                 ORDER BY de.from_date DESC";
    
    $stmt_dept = $conn->prepare($sql_dept);
    if (!$stmt_dept) {
        error_log("Erreur préparation requête dept_history: " . mysqli_error($conn));
        return ['departments' => false, 'titles' => false, 'salaries' => false];
    }
    
    $stmt_dept->bind_param('i', $emp_no);
    $stmt_dept->execute();
    $dept_history = $stmt_dept->get_result();
    
    $sql_title = "SELECT title, from_date, to_date 
                  FROM titles 
                  WHERE emp_no = ? 
                  ORDER BY from_date DESC";
    
    $stmt_title = $conn->prepare($sql_title);
    if (!$stmt_title) {
        error_log("Erreur préparation requête title_history: " . mysqli_error($conn));
        return ['departments' => $dept_history, 'titles' => false, 'salaries' => false];
    }
    
    $stmt_title->bind_param('i', $emp_no);
    $stmt_title->execute();
    $title_history = $stmt_title->get_result();
    
    $sql_salary = "SELECT salary, from_date, to_date 
                   FROM salaries 
                   WHERE emp_no = ? 
                   ORDER BY from_date DESC 
                   LIMIT 10";
    
    $stmt_salary = $conn->prepare($sql_salary);
    if (!$stmt_salary) {
        error_log("Erreur préparation requête salary_history: " . mysqli_error($conn));
        return ['departments' => $dept_history, 'titles' => $title_history, 'salaries' => false];
    }
    
    $stmt_salary->bind_param('i', $emp_no);
    $stmt_salary->execute();
    $salary_history = $stmt_salary->get_result();
    
    return [
        'departments' => $dept_history,
        'titles' => $title_history,
        'salaries' => $salary_history
    ];
}

function executerRecherche($departement, $nom, $ageMin, $ageMax, $offset, $limit)
{
    $conn = dbconnect();

    $params = [];
    $types = "";
    $conditions = [];

    $baseQuery = "FROM employees e
                  JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date = '9999-01-01'
                  JOIN departments d ON de.dept_no = d.dept_no";

    if (!empty($departement)) {
        $conditions[] = "d.dept_name LIKE ?";
        $params[] = '%' . $departement . '%';
        $types .= 's';
    }

    if (!empty($nom)) {
        $conditions[] = "e.last_name LIKE ?";
        $params[] = '%' . $nom . '%';
        $types .= 's';
    }

    if ($ageMin !== '') {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= ?";
        $params[] = $ageMin;
        $types .= 'i';
    }

    if ($ageMax !== '') {
        $conditions[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= ?";
        $params[] = $ageMax;
        $types .= 'i';
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = " WHERE " . implode(" AND ", $conditions);
    }

    $countSql = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
    $countStmt = $conn->prepare($countSql);

    if ($countStmt && !empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }

    if ($countStmt) {
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalResults = $countResult->fetch_assoc()['total'];
        $countStmt->close();
    } else {
        $totalResults = 0;
    }

    $sql = "SELECT e.last_name, d.dept_name, TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) AS age "
        . $baseQuery . $whereClause
        . " ORDER BY e.last_name, d.dept_name LIMIT ?, ?";

    $finalParams = $params;
    $finalParams[] = $offset;
    $finalParams[] = $limit;
    $finalTypes = $types . 'ii';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Erreur préparation requête recherche: " . mysqli_error($conn));
        return ['results' => false, 'total' => 0];
    }

    $stmt->bind_param($finalTypes, ...$finalParams);
    $stmt->execute();

    $result = $stmt->get_result();

    return [
        'results' => $result,
        'total' => $totalResults
    ];
}
?>