<?php
require_once "config/Database.php";
require_once "repositories\interface\IProductRepository.php";

class ProductRepository implements IProductRepository {
    private $databaseConnection;
    private Database $database;
    
    public function __construct() {
        $this->database =  Database::getInstance();
        $this->databaseConnection = $this->database->getConnection();
    }

    public function GetAllProduct() 
    {
        global $conn;
    
    $query = "SELECT p.ProductId, p.ProductName, pd.ProductPrice, pd.ProductDate
              FROM PRODUCT p
              JOIN PRODUCTDETAILS pd ON p.ProductId = pd.ProductId";

    $result = mysqli_query($conn, $query);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    echo json_encode($products);
    }

    public function GetLatestPriceOfTheProduct() 
    {
    global $conn;        
    $query = "SELECT p.ProductId, p.ProductName, pd.ProductPrice, pd.ProductDate
              FROM PRODUCT p
              JOIN PRODUCTDETAILS pd ON p.ProductId = pd.ProductId
              WHERE pd.ProductDate = (
                  SELECT MAX(ProductDate) FROM PRODUCTDETAILS WHERE ProductId = p.ProductId
              )";

    $result = mysqli_query($conn, $query);
    
    $latestProducts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $latestProducts[] = $row;
    }

    echo json_encode($latestProducts);
    }

    public function GetProductById($productId) 
    {
        $query = "SELECT 
                    Product.ProductId
                    , Product.ProductName
                    , ProductDetails.ProductPrice
                    , ProductDetails.ProductDate
                   FROM Product 
                   INNER JOIN ProductDetails ON ProductDetails.ProductId = Product.ProductId
                   WHERE Product.ProductId = :productId";

        $params = [
            ':productId' => $productId
        ];

        return $this->ExecuteSqlQuery($query, $params);
    }

    private function ExecuteSqlQuery(string $query, array $params) {
        $statementObject = $this->databaseConnection->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }
}
